<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01/04/14
 * Time: 18:09
 */

namespace AM\MusicBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MusicFilesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('song', 'file', array(
            ))
            ->add('cover', 'file', array(
                'required' => 'false',
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AM\MusicBundle\Entity\MusicFiles'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'am_musicbundle_musicfiles';
    }
} 