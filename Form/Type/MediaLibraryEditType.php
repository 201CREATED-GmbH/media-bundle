<?php

namespace C201\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaLibraryEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, ['required' => false]);

        if ($options['contexts']) {
            $builder
                ->add(
                    'context',
                    ChoiceType::class,
                    [
                        'required' => true,
                        'choices'  => $options['contexts'],
                    ]
                );
        } else {
            $builder
                ->add(
                    'context',
                    null,
                    [
                        'required' => false,
                    ]
                );
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['contexts' => []]);
    }

    public function getBlockPrefix()
    {
        return 'c201_media__media_libarary_edit';
    }
}
