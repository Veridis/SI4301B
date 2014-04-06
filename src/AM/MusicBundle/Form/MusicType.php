<?php

namespace AM\MusicBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MusicType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Music title',
                )
            ))
            ->add('album', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Album',
                )
            ))
            ->add('style', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Rock, Electro, House, R&B ...',
                )
            ))
            ->add('duration', 'integer', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'In seconds',
                )
            ))
            ->add('musicfiles', new MusicFilesType())
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AM\MusicBundle\Entity\Music',
            'cascade_validation' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'am_musicbundle_music';
    }
}
