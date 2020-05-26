<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class VideoType extends AbstractType
{
    /*
    *{@inheritdoc}
    */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $video = $event->getData();
                $form = $event->getForm();
                if ($video  && $video->getId() !== null) {
                    $form->add('url', HiddenType::class, [
                        'attr' => [
                            'placeholder' => 'Enter une URL Youtube ou Daylimotion',
                        ],
                        'constraints' => [
                            new Regex([
                                'pattern' => '#(http|https)://(www.youtube.com|www.dailymotion.com)/#',
                                'match' => 'true',
                                'message' => 'Votre lien n\'est pas valide',
                            ]),
                        ],
                    ]);
                } else {
                    $form->add('url', TextType::class, [

                        'attr' => [
                            'placeholder' => 'Enter une URL Youtube ou Daylimotion',
                        ],
                        'constraints' => [
                            new Regex([
                                'pattern' => '#(http|https)://(www.youtube.com|www.dailymotion.com)/#',
                                'match' => 'true',
                                'message' => 'Votre lien n\'est pas valide',
                            ]),
                        ],
                    ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
            'translation_domain' => false,
        ]);
    }
}
