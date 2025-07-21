<?php

// src/DataFixtures/UserFixtures.php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Adventurer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            ['email' => 'admin@example.com', 'roles' => ['ROLE_ADMIN'], 'password' => 'admin123', 'adventurers' => ['Conan', 'Lara']],
            ['email' => 'user@example.com',  'roles' => ['ROLE_USER'],  'password' => 'user123',  'adventurers' => ['Frodo', 'Arya']],
        ];

        foreach ($usersData as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles($data['roles']);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $data['password'])
            );

            $manager->persist($user);


        }

        $manager->flush();
    }
}
