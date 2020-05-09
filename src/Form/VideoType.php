<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter une URL Youtube ou Daylimotion',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une url'
                    ]),
                    new Regex([
                        'pattern' => '#(http|https)://(www.youtube.com|www.dailymotion.com)/#',
                        'match' => 'true',
                        'message' => 'Votre lien n\'est pas valide',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
