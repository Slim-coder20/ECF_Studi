<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


class VehiculeTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque', TextType::class, [
                'label' => 'Marque',
                'attr' => [
                    'placeholder' => 'Entrez la marque du véhicule',
                    
                ],
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
                'attr' => [
                    'placeholder' => 'Entrez le modèle du véhicule',
                ],
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])
            ->add('couleur', textType::class, [
                'label' => 'Couleur',
                'attr' => [
                    'placeholder' => 'Entrez la couleur du véhicule',
                ],
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])
            ->add('plaqueImmatriculation',textType::class, [
                'label' => 'Plaque d\'immatriculation',
                'attr' => [
                    'placeholder' => 'Entrez la plaque d\'immatriculation du véhicule',
                ],
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])
            ->add('datePremiereImmatriculation',DateType::class, [
                'label' => 'Date de première immatriculation',
                'attr' => [
                    'placeholder' => 'Entrez la date de première immatriculation',
                ],
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])
            ->add('nbPlaces',IntegerType::class, [
                'label' => 'Nombre de places',
                'attr' => [
                    'placeholder' => 'Entrez le nombre de places',
                ],
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])
            ->add('isElectrique', CheckboxType::class, [
                'label' => 'Véhicule électrique',
                'required' => false,
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])
            ->add('accepteFumeur', CheckboxType::class, [
                'label' => 'Accepte les fumeurs',
                'required' => false,
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])
            ->add('accepteAnimaux', CheckboxType::class, [
                'label' => 'Accepte les animaux',
                'required' => false,
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])

            ->add('photo', FileType::class, [
                'label' => 'Photo de Véhicule',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF, WebP)',
                    ]),
                ],
                'label_attr' => [
                    'class' => 'text-success',
                ],
                'attr' => ['placeholder' => 'Entrez l\'URL de votre photo de Véhicule'],
            ])
            
            ->add('proprietaire', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'pseudo',
                'label' => 'Propriétaire',
                'label_attr' => [
                    'class' => 'text-success',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
