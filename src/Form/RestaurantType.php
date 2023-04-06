<?php

namespace App\Form;

use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom du resto"
            ])
            ->add('director', TextType::class, [
                'label' => "Nom du directeur"
            ])
            ->add('address', AddressType::class, [
                'mapped' => true,
                'label' => "Saisir l'adresse"
            ])
            ->add('plats', PlatType::class, [
                'mapped' => true,
                'label' => "Saisir les plats"
            ])
            ->add('menu', type: MenuType::class, options: [
                'mapped' => true,
                'label' => "Saisir le menu"
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
