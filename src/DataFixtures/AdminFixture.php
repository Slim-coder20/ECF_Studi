<?php 
namespace App\DataFixtures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 

class AdminFixture extends Fixture 
{

    private $passwordhasher;

    public function __construct(UserPasswordHasherInterface $passwordhasher) 
    {
        $this->passwordhasher = $passwordhasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin.ecoride9@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPseudo('adminEcoride9');
        $admin->setFirstName('Slim');
        $admin->setLastName('abida');

        $hashedPassword = $this->passwordhasher->hashPassword(
            $admin,
            'admin1234'
        );
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);
        $manager->flush();
    
    
    }








}

