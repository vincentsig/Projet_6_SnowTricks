<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ForgottenPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add(
                'email',
                RepeatedType::class,
                array(
                    'type' => EmailType::class,
                    'first_options'  => array('label' => 'Email'),
                    'second_options' => array('label' => 'Ressaisir votre Email'),
                    'invalid_message' => 'Les champs ne correspondent pas'
                )
            );
    }
}
