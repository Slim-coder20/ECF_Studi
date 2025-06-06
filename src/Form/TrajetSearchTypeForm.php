<?php
namespace App\Form;

use App\Model\TrajetSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

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
            ])
            ->add('ecoloOnly', CheckboxType::class, [
                'label' => 'Trajets écologiques uniquement',
                'required' => false,
                
            ])
            ->add('prixMax', IntegerType::class, [
                'label' => 'Prix maximum',
                'required' => false,
            ])
            ->add('dureeMax', IntegerType::class, [
                'label' => 'Durée maximum (en minutes)',
                'required' => false,
            ])
            ->add('noteMin', IntegerType::class, [
                'label' => 'Note minimum',
                'required' => false,
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
