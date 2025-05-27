<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Trajet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrajetFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
{
    // Récuprérer l'utilisateur depuis l'email via le getRepository // 
    $lucie = $manager->getRepository(User::class)->findOneBy(['email' => 'luciedupont@gmail.com']);

    if ($lucie) {
        $trajetLucie = new Trajet();
        $trajetLucie->setVilleDepart('Lille');
        $trajetLucie->setVilleArrivee('Paris');
        $trajetLucie->setDateDepart(new \DateTime('+1 day 07:30'));
        $trajetLucie->setDateArrivee(new \DateTime('+1 day 10:00'));
        $trajetLucie->setNbPlaces(2);
        $trajetLucie->setPrix(15.00);
        $trajetLucie->setChauffeur($lucie);

        $manager->persist($trajetLucie);
    } else {
        dump('Lucie introuvable — pas de trajet créé pour elle.');
    }

    // 
    $existing = $manager->getRepository(User::class)->findOneBy(['email' => 'chauffeur@test.com']);

    if (!$existing) {
        $chauffeur = new User();
        $chauffeur->setEmail('chauffeur@test.com');
        $chauffeur->setPassword('password'); // non hashé pour tests
        $chauffeur->setFirstName('John');
        $chauffeur->setLastName('Doe');
        $chauffeur->setPseudo('GreenDriver');
        $chauffeur->setRoles(['ROLE_USER']);
        $chauffeur->setPhoto('default.jpg');

        $manager->persist($chauffeur);

        for ($i = 0; $i < 5; $i++) {
            $trajet = new Trajet();
            $trajet->setVilleDepart('Paris');
            $trajet->setVilleArrivee('Lyon');
            $trajet->setDateDepart(new \DateTime("+$i days 08:00"));
            $trajet->setDateArrivee(new \DateTime("+$i days 12:00"));
            $trajet->setNbPlaces(3);
            $trajet->setPrix(25 + $i * 5);
            $trajet->setChauffeur($chauffeur);

            $manager->persist($trajet);
        }
    } else {
        dump('chauffeur@test.com existe déjà — utilisateur ignoré');
    }

    $manager->flush();
}























































}
