<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 05/04/14
 * Time: 14:38
 */

namespace AM\MusicBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea', array(
                'attr' => array(
                    'class' => 'form-control comment',
                    'placeholder' => 'Post your comment !',
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AM\MusicBundle\Entity\Comment',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'am_musicbundle_comment';
    }
} 