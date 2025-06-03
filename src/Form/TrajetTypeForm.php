<?php

namespace App\Form;

use App\Entity\Trajet;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;



class TrajetTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('villeDepart', TextType::class, [
                'label' => 'Ville de départ',
                'attr' => ['placeholder' => 'Entrez la ville de départ'],
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])
           

            ->add('villeArrivee', TextType::class, [
                'label' => 'Ville d\'arrivée',
                'attr' => ['placeholder' => 'Entrez la ville d\'arrivée'],
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])
            ->add('dateDepart',DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de départ',
                'input' => 'datetime',
                'label_attr' => [
                    'class' => 'text-success',
                ],
                'attr' => ['placeholder' => 'Sélectionnez la date de départ'],
            ])
            ->add('dateArrivee', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'arrivée',
                'input' => 'datetime',
                'label_attr' => [
                    'class' => 'text-success',
                ],
                
            ])
            ->add('nbPlaces',IntegerType::class, [
                'label' => 'Nombre de places',
                'attr' => ['placeholder' => 'Entrez le nombre de places disponibles'],
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])
            ->add('prix', IntegerType::class, [
            'label' => 'Prix',
            'label_attr' => [
            'class' => 'text-success',
    ],
            'constraints' => [
            new GreaterThanOrEqual([
            'value' => 2,
            'message' => 'Le prix doit être au minimum de 2 crédits.',
        ]),
    ],
])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trajet::class,
        ]);
    }
}
