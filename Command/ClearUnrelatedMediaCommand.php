<?php

namespace C201\MediaBundle\Command;

use C201\MediaBundle\Model\Object\ObjectField;
use C201\MediaBundle\Model\Object\ObjectFileContainer;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ClearUnrelatedMediaCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('c201-media:clear-unrelated')
            ->setDescription('Hello PhpStorm')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Remove all the files from the filesystem')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $searchIn = [];
        $whitelist = [];

        $media = $this->getContainer()->get('c201_media.media');
        $pathParser = $this->getContainer()->get('c201_media.model.path_parser');
        $configuration = $this->getContainer()->get('c201_media.configuration');
        $webDir = realpath($this->getContainer()->getParameter('kernel.root_dir').'/../web');

        foreach ($configuration->getClassesUnderControl() as $class) {
            try {
                $all = $this->getContainer()->get('doctrine_mongodb')->getRepository($class)->findAll();
                foreach ($all as $object) {
                    $objectConfiguration = $configuration->getObjectConfiguration($object);

                    $folderHasContents = false;
                    foreach ($objectConfiguration->getFields() as $field) {
                        $objectField = new ObjectField($object, $field);
                        $objectFieldConfiguration = $configuration->getObjectFieldConfiguration($objectField);
                        if ('file' !== $objectFieldConfiguration->getStorage()) {
                            continue;
                        }
                        if (!$objectField->getValue()) {
                            continue;
                        }

                        $whitelist[] = $webDir . $media->getPath($objectField);

                        $folderHasContents = true;
                    }
                    if ($folderHasContents) {
                        $searchIn[] = $webDir . '/uploads/' . $pathParser->parse($objectConfiguration->getUploadPath(), new ObjectFileContainer($object));
                    }
                }
            }
            catch (MappingException $e) {
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            }
        }

        // `$searchIn` now contains all the folders we are allowed to watch into for deleting
        // `$whitelist` now contains all the files better NOT! to remove

        $finder = new Finder();
        $finder->files()->in($webDir.'/uploads')->exclude(['media']);

        $unrelated = [];

        foreach ($finder as $file) {
            // skip all files we are not allowed to handle because they come from a different directory
            if (!in_array($file->getPath(), $searchIn)) {
                continue;
            }

            // so when we have a file here that is not used from the media system, we will add it to `$unrelated` array
            if (!in_array($file->getPathname(), $whitelist)) {
                $unrelated[] = $file;
            }
        }

        if (!$unrelated) {
            $output->writeln('Nothing to delete. Everything looks fine! Exiting.');
            return;
        }

        // show info message
        foreach ($unrelated as $file) {
            $output->writeln(sprintf('The file <info>%s</info> is unrelated and will be deleted.', $file->getRelativePathname()));
        }


        $dialog = $this->getHelper('dialog');
        if ($input->getOption('force')) {
            if ($dialog->askConfirmation($output, '<question>Deleting all the files?</question>', false)) {
                $filesystem = new Filesystem();
                $filesystem->remove($unrelated);
            }
        }
    }
}
