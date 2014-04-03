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
            ->add('username', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Artist name, band name ...',
                )
            ))
            ->add('email', 'email', array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                )
            ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'first_options' => array(
                    'label' => 'Password',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Password',
                    )
                ),
                'second_options' => array(
                    'label' => 'Confirm',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Confirm password',
                    )
                ),
                'invalid_message' => 'The passwords are not the same',
            ));
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