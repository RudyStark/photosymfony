<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'First Name']
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Last Name']
            ])
            ->add('age', IntegerType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Age']
            ])
            ->add('phone', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Phone']
            ])
            ->add('address', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Address']
            ])
            ->add('postalCode', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Postal Code']
            ])
            ->add('city', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'City']
            ])
            ->add('country', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Country']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
