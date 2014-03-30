<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/03/14
 * Time: 12:10
 */

namespace AM\UserBundle\Form;

use AM\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('plainPassword', 'repeated', array(
                'type' => 'password'
            ))
            ->add('email', 'email')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AM\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'am_userbundle_user';
    }
} 