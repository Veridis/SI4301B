<?php

namespace AM\AdminBundle\Form;

use AM\AdminBundle\Entity\Administrator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdministratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'disabled' => true
            ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password'
            ))
            ->add('enabled')
            ->add('roles', 'choice', array(
                'choices' => Administrator::getExistingRoles(),
                'multiple' => true,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AM\AdminBundle\Entity\Administrator'
        ));
    }

    public function getName()
    {
        return 'am_adminbundle_administrator';
    }
} 