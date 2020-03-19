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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('category', EntityType::class, array(
                'class' => Category::class,
                'choice_label' => 'getName',
                'placeholder' => 'category', 'label' => 'Category',
            ))

            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $trick = $event->getData();
                $form = $event->getForm();
                // checks if the trick object is "new"
                // If no data is passed to the form, the data is "null".
                // This should be considered a new "trick" so constraints is NotBlank for the imageFiles
                if (!$trick || null === $trick->getId()) {
                    $form->add('imageFiles', FileType::class, [
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Vous devez au moins ajouter une image'
                            ])
                        ],
                        'required' => false,
                        'multiple' => true
                    ]);
                } else {
                    // if the trick object exist already ImageFiles can be Blank.
                    $form->add('imageFiles', FileType::class, [
                        'required' => false,
                        'multiple' => true
                    ]);
                }
            })

            ->add('videos', CollectionType::class, [
                'entry_type'   => VideoType::class,
                'entry_options' => ['label' => false],
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'createdAt' => Null
        ]);
    }
}
