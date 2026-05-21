<?php declare(strict_types=1);

namespace C201\MediaBundle\Form\DataTransformer;

use C201\MediaBundle\Manager\MediaManager;
use Symfony\Component\Form\DataTransformerInterface;

class MediaTransformer implements DataTransformerInterface
{
    public function __construct (
        private readonly MediaManager $manager,
        private readonly array $options,
    )
    {
    }


    protected function isFromLibrary () : bool
    {
        return $this->options['media_library_owner'] && $this->options['media_library_context'];
    }


    public function transform (mixed $value) : mixed
    {
        if (null === $value) {
            return $this->cleanTransform([
                'value' => '',
                'library' => '',
                'upload' => '',
                'remove' => false,
            ]);
        }

        if (\preg_match('@^library:([^:]+):([^:]+)$@', $value, $matches)) {
            [, $id,] = $matches;

            return $this->cleanTransform([
                'value' => $value,
                'library' => $this->manager->findOneByOwnerAndId($this->options['media_library_owner'], $id),
                'upload' => '',
                'remove' => false,
            ]);
        }

        return $this->cleanTransform([
            'value' => $value,
            'library' => '',
            'upload' => '',
            'remove' => false,
        ]);
    }


    protected function cleanTransform (array $values) : array
    {
        if (!$this->isFromLibrary()) {
            unset($values['library']);
        }

        return $values;
    }


    public function reverseTransform (mixed $value) : mixed
    {
        if (!$value || $value['remove']) {
            return null;
        }

        if ($this->isFromLibrary() && $value['library']) {
            return \sprintf('library:%s:%s', $value['library']->getId(), 'head');
        }

        if ($value['upload']) {
            return $value['upload'];
        }

        return $value['value'];
    }
}
