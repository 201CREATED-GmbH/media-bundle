<?php

namespace C201\MediaBundle\Form\Type;

use C201\MediaBundle\Entity\Media;
use C201\MediaBundle\Form\DataTransformer\MediaTransformer;
use C201\MediaBundle\Manager\MediaManager;
use C201\MediaBundle\Repository\MediaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends AbstractType
{
    /**
     * @var MediaManager
     */
    private $mediaManager;

    /**
     * @param MediaManager $mediaManager
     */
    public function __construct(MediaManager $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', HiddenType::class)
            ->addViewTransformer(new MediaTransformer($this->mediaManager, $options))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                if ($options['can_remove'] && $event->getData()) {
                    $event->getForm()->add('remove', CheckboxType::class);
                }
            })
        ;

        if ($options['can_upload']) {
            $builder->add('upload', FileType::class);
        }

        if ($options['media_library_owner'] && $options['media_library_context']) {
            $builder->add(
                'library',
                EntityType::class,
                [
                    'required'      => $options['required'],
                    'class'         => Media::class,
                    'query_builder' => function (MediaRepository $repository) use ($options) {
                        return $repository->queryBuilderForOwnerAndContext(
                            $options['media_library_owner'],
                            $options['media_library_context']
                        );
                    }
                ]
            );
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['preview'] = $options['preview'];
        $view->vars['can_upload'] = $options['can_upload'];
        $view->vars['can_remove'] = $options['can_remove'];
        $view->vars['media_library_owner'] = $options['media_library_owner'];
        $view->vars['media_library_context'] = $options['media_library_context'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'preview'               => false,
                'can_upload'            => false,
                'can_remove'            => false,
                // can choose from media library
                'media_library_owner'   => null,
                'media_library_context' => null,
            ]
        );
    }
    public function getBlockPrefix()
    {
        return 'media';
    }
}
