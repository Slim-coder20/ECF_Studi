<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\Trajet;
use App\Entity\User;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;




class AvisTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', ChoiceType::class,[
                'label' => 'Note', 
                'label_attr' => [
                    'class' => 'text-success',
                ],
                'choices' => [
                    '⭐⭐' => 2,
                    '⭐⭐⭐' => 3,
                    '⭐⭐⭐⭐' => 4,
                    '⭐⭐⭐⭐⭐' => 5,
                ],
                'expanded' => true,
                'multiple' => false, 
               
                
                ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Votre Commentaire',
                'label_attr' => [
                    'class' => 'text-success',
                ],
                'attr' => [
                    'placeholder' => 'Laissez un commentaire (facultatif)',
                    'rows' => 4,
                ],
            ])
         
           
            ->add('auteur', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('chauffeur', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('trajet', EntityType::class, [
                'class' => Trajet::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
