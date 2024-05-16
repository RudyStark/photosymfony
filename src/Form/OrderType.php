<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\StatutOrder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deliveryMode')
            ->add('paymentMode')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('modifiedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => 'id',
            ])
            ->add('statutOrder', EntityType::class, [
                'class' => StatutOrder::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
