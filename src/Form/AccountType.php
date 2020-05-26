<?php

namespace App\Form;

use App\Entity\Profile;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AccountType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('presentation', TextType::class, ['label' => 'Présentation'])
            ->add(
                'avatar',
                FileType::class,
                [
                    'attr' => [
                        'placeholder' => 'Ajouter ou modifier votre avatar'
                    ],
                    'required' => false,
                    'mapped' => false,

                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/jpg'
                            ],
                            'mimeTypesMessage' => 'Le document doit être une image avec une extension en .jpeg .jpg .png'
                        ])
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
            'translation_domain' => false,
        ]);
    }
}
