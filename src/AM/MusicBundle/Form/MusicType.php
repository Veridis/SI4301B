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
            ->add('title')
            ->add('album')
            ->add('coverPath')
            ->add('style')
            ->add('path')
            ->add('duration')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AM\MusicBundle\Entity\Music'
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
