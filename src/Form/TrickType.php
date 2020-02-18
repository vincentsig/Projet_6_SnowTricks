<?php

namespace App\Form;


use App\Entity\Trick;
use App\Form\VideoType;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('category', EntityType::class, array('class' => Category::class,
                'choice_label' => 'getName',
                'placeholder' => 'category', 'label' => 'Category', ))
            ->add('imageFiles', FileType::class, [
                'required' => false,
                'multiple' => true
            ])
            ->add('videos', CollectionType::class, [
                'entry_type'   => VideoType::class,
                'entry_options' => ['label' => false],
                'allow_add'    => true,
                'allow_delete' => true
            ])
                
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'createdAt' => Null
        ]);
    }
}
