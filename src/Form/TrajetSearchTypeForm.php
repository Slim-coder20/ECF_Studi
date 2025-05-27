<?php
namespace App\Form;

use App\Model\TrajetSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrajetSearchTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('villeDepart', TextType::class, [
                'label' => 'Ville de départ',
            ])
            ->add('villeArrivee', TextType::class, [
                'label' => 'Ville d\'arrivée',
            ])
            ->add('date', DateType::class, [
                'label' => 'Date du trajet',
                'widget' => 'single_text',
                'input' => 'datetime',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TrajetSearch::class, 
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
