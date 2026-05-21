<?php declare(strict_types=1);

namespace C201\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaLibraryEditType extends AbstractType
{
    public function buildForm (FormBuilderInterface $builder, array $options) : void
    {
        $builder->add('name', null, ['required' => false]);

        if ($options['contexts']) {
            $builder->add('context', ChoiceType::class, [
                'required' => true,
                'choices' => $options['contexts'],
            ]);
        }
        else {
            $builder->add('context', null, ['required' => false]);
        }
    }


    public function configureOptions (OptionsResolver $resolver) : void
    {
        $resolver->setDefaults(['contexts' => []]);
    }


    public function getBlockPrefix () : string
    {
        return 'c201_media__media_libarary_edit';
    }
}
