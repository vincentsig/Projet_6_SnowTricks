<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\VideoType;
use App\Entity\Category;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Saisissez le nom de la figure',
                ]
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'placeholder' => 'Decrivez les spécificités de la figure',
                ]
            ])
            ->add('category', EntityType::class, array(
                'class' => Category::class,
                'choice_label' => 'getName',
                'placeholder' => 'Choisissez un groupe de figure',
                'label' => 'Groupe',
            ))

            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $trick = $event->getData();
                $form = $event->getForm();
                // checks if the trick object is "new"
                // If no data is passed to the form, the data is "null".
                // This should be considered a new "trick" so constraints is NotBlank for the imageFiles
                if (!$trick || null === $trick->getId()) {
                    $form->add('imageFiles', FileType::class, [
                        'label' => 'Image(s)',
                        'attr' => [
                            'placeholder' => 'Veuillez choisir une image de type .jpg, .jpeg ou .png '
                        ],
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Vous devez au moins ajouter une image'
                            ])
                        ],
                        'multiple' => true,
                    ]);
                } else {
                    // if the trick object exist already ImageFiles can be Blank.
                    $form->add('imageFiles', FileType::class, [
                        'label' => 'Image(s)',
                        'attr' => [
                            'placeholder' => 'Veuillez choisir une image de type .jpg, .jpeg ou .png '
                        ],
                        'required' => false,
                        'multiple' => true,
                    ]);
                }
            });

        $builder
            ->add('videos', CollectionType::class, [
                'entry_type'   => VideoType::class,
                'entry_options' => ['label' => false],
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'allow_extra_fields' => true,
            'createdAt' => null,
            'translation_domain' => false,
        ]);
    }
}
