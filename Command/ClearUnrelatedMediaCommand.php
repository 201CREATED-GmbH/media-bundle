<?php declare(strict_types=1);

namespace C201\MediaBundle\Command;

use C201\MediaBundle\Model\Configuration\MediaConfiguration;
use C201\MediaBundle\Model\Media;
use C201\MediaBundle\Model\Object\ObjectField;
use C201\MediaBundle\Model\Object\ObjectFileContainer;
use C201\MediaBundle\Model\Storage\PathParser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

#[AsCommand(name: 'c201-media:clear-unrelated', description: 'Removes unrelated media files from the filesystem')]
class ClearUnrelatedMediaCommand extends Command
{
    public function __construct (
        private readonly Media $media,
        private readonly PathParser $pathParser,
        private readonly MediaConfiguration $configuration,
        private readonly ManagerRegistry $doctrine,
        private readonly string $projectDir,
    )
    {
        parent::__construct();
    }


    protected function configure () : void
    {
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Remove all the files from the filesystem');
    }


    protected function execute (InputInterface $input, OutputInterface $output) : int
    {
        $webDir = \realpath($this->projectDir . '/public');
        $searchIn = [];
        $whitelist = [];

        foreach ($this->configuration->getClassesUnderControl() as $class) {
            try {
                $all = $this->doctrine->getRepository($class)->findAll();

                foreach ($all as $object) {
                    $objectConfiguration = $this->configuration->getObjectConfiguration($object);

                    $folderHasContents = false;

                    foreach ($objectConfiguration->getFields() as $field) {
                        $objectField = new ObjectField($object, $field);
                        $objectFieldConfiguration = $this->configuration->getObjectFieldConfiguration($objectField);

                        if ('file' !== $objectFieldConfiguration->getStorage()) {
                            continue;
                        }

                        if (!$objectField->getValue()) {
                            continue;
                        }

                        $whitelist[] = $webDir . $this->media->getPath($objectField);
                        $folderHasContents = true;
                    }

                    if ($folderHasContents) {
                        $searchIn[] = $webDir . '/uploads/' . $this->pathParser->parse($objectConfiguration->getUploadPath(), new ObjectFileContainer($object));
                    }
                }
            } catch (\Exception $e) {
                $output->writeln(\sprintf('<error>%s</error>', $e->getMessage()));
            }
        }

        $finder = new Finder();
        $finder->files()->in($webDir . '/uploads')->exclude(['media']);

        $unrelated = [];

        foreach ($finder as $file) {
            if (!\in_array($file->getPath(), $searchIn, true)) {
                continue;
            }

            if (!\in_array($file->getPathname(), $whitelist, true)) {
                $unrelated[] = $file;
            }
        }

        if (!$unrelated) {
            $output->writeln('Nothing to delete. Everything looks fine! Exiting.');
            return Command::SUCCESS;
        }

        foreach ($unrelated as $file) {
            $output->writeln(\sprintf('The file <info>%s</info> is unrelated and will be deleted.', $file->getRelativePathname()));
        }

        if ($input->getOption('force')) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('<question>Deleting all the files?</question> ', false);

            if ($helper->ask($input, $output, $question)) {
                (new Filesystem())->remove($unrelated);
            }
        }

        return Command::SUCCESS;
    }
}
