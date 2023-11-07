<?php

namespace App\DataFixtures;

use App\Entity\Instructeur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher) {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Add First Instructeur
        $instructeur = new Instructeur();

        $instructeur->setNom("Hanafi Elmahdi");
        $instructeur->setEmail("mahdi@gmail.com");
        $instructeur->setPhone("0696130475");

        $user = new User();
        
        $user->setEmail('admin@admin.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setInstructeur($instructeur);
        // encode the plain password
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                "admin"
            )
        );

        $manager->persist($user);
        $manager->flush();
    }
}
