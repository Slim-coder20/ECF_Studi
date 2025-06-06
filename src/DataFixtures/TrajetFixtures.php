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
        // 1. Récupérer Lucie si elle existe
        $lucie = $manager->getRepository(User::class)->findOneBy(['email' => 'luciedupont@gmail.com']);

        if ($lucie) {
            // Ajouter une note à Lucie si pas déjà faite
            $lucie->setNote(4.5); // Note sur 5 par exemple
            $manager->persist($lucie);

            // Créer un trajet pour Lucie
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

        // 2. Créer un autre utilisateur de test si nécessaire
        $existing = $manager->getRepository(User::class)->findOneBy(['email' => 'chauffeur@test.com']);

        if (!$existing) {
            $chauffeur = new User();
            $chauffeur->setEmail('chauffeur@test.com');
            $chauffeur->setPassword('password');
            $chauffeur->setFirstName('John');
            $chauffeur->setLastName('Doe');
            $chauffeur->setPseudo('GreenDriver');
            $chauffeur->setRoles(['ROLE_USER']);
            $chauffeur->setPhoto('default.jpg');
            //$chauffeur->setNote(4.0);

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
