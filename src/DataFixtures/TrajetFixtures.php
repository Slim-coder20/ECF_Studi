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
        // Création d'un utilisateur fictif (chauffeur)
        $chauffeur = new User();
        $chauffeur->setEmail('chauffeur@test.com');
        $chauffeur->setPassword('password'); 
        $chauffeur->setFirstName('John');
        $chauffeur->setLastName('Doe');
        $chauffeur->setPseudo('GreenDriver');
        $chauffeur->setRoles(['ROLE_USER']);

        $manager->persist($chauffeur);

        // Création de trajets
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

        $manager->flush();
    }
}
