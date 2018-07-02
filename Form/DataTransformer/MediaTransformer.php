<?php

namespace C201\MediaBundle\Form\DataTransformer;

use C201\MediaBundle\Manager\MediaManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MediaTransformer implements DataTransformerInterface
{
    /**
     * @var MediaManager
     */
    private $manager;

    /**
     * @var array
     */
    private $options;

    /**
     * can_upload
     * can_remove
     *
     * @param MediaManager $manager
     * @param              $options
     */
    public function __construct(MediaManager $manager, $options)
    {
        $this->manager = $manager;
        $this->options = $options;
    }

    /**
     * Returns true when this file will be stored to/retrieved from library
     *
     * @return bool
     */
    protected function isFromLibrary()
    {
        return $this->options['media_library_owner'] && $this->options['media_library_context'];
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * This method is called on two occasions inside a form field:
     *
     * 1. When the form field is initialized with the data attached from the datasource (object or array).
     * 2. When data from a request is submitted using {@link Form::submit()} to transform the new input data
     *    back into the renderable format. For example if you have a date field and submit '2009-10-10'
     *    you might accept this value because its easily parsed, but the transformer still writes back
     *    "2009/10/10" onto the form field (for further displaying or other purposes).
     *
     * This method must be able to deal with empty values. Usually this will
     * be NULL, but depending on your implementation other empty values are
     * possible as well (such as empty strings). The reasoning behind this is
     * that value transformers must be chainable. If the transform() method
     * of the first value transformer outputs NULL, the second value transformer
     * must be able to process that value.
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        // when there is no value, everything should be empty
        if (null === $value) {
            return $this->cleanTransform(
                [
                    'value'   => '',
                    'library' => '',
                    'upload'  => '',
                    'remove'  => false,
                ]
            );
        }

        // when file comes from the media library, we will display the selected file there again
        if (preg_match('@^library:([^:]+):([^:]+)$@', $value, $matches)) {
            list (, $id, $version) = $matches;

            return $this->cleanTransform(
                [
                    'value'   => $value,
                    'library' => $this->manager->findOneByOwnerAndId($this->options['media_library_owner'], $id),
                    'upload'  => '',
                    'remove'  => false,
                ]
            );
        }

        // otherwise we will only set $value which is hidden
        return $this->cleanTransform(
            [
                'value'   => $value,
                'library' => '',
                'upload'  => '',
                'remove'  => false,
            ]
        );
    }

    /**
     *
     */
    protected function cleanTransform($values)
    {
        if (!$this->isFromLibrary()) {
            unset($values['library']);
        }

        return $values;
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * This method is called when {@link Form::submit()} is called to transform the requests tainted data
     * into an acceptable format for your data processing/model layer.
     *
     * This method must be able to deal with empty values. Usually this will
     * be an empty string, but depending on your implementation other empty
     * values are possible as well (such as empty strings). The reasoning behind
     * this is that value transformers must be chainable. If the
     * reverseTransform() method of the first value transformer outputs an
     * empty string, the second value transformer must be able to process that
     * value.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        if (!$value || $value['remove']) {
            return null;
        }

        // when user has chosen from the media library, we will create a string like
        // library:<mongo-id-of-media>:<version-of-file-in-media>
        // version can be head wich will be the last version automatically
        if ($this->isFromLibrary() && $value['library']) {
            return sprintf('library:%s:%s', $value['library']->getId(), 'head');
        }

        // when a file was uploaded, put this file into the field to handle the upload with the c201 media bundle
        if ($value['upload']) {
            return $value['upload'];
        }

        // otherwise, send back the old value! (nothing has happened)
        return $value['value'];
    }
}
