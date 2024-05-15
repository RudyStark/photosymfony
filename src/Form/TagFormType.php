<?php

namespace App\Form;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'label' => 'Tag',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Choisir un tag',
                'autocomplete' => 'true',
                'multiple' => true,
                'query_builder' => function (TagRepository $er) {
                    return $er->findMostUsedTags(4);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
