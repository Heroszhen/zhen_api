<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager):void 
    {
        $user = new User();
        $plainpassword = "aaaaaaaa";
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainpassword
        );
        $user->setPassword($hashedPassword);
        $apiKey = bin2hex(openssl_random_pseudo_bytes(16));

        $user
            ->setEmail("herosgogogogo@gmail.com")
            ->setRoles(["ROLE_ADMIN","ROLE_USER"])
            ->setApiKey($apiKey)
        ;

        $manager->persist($user);

        $user = new User();
        $plainpassword = "bbbbbbbb";
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainpassword
        );
        $user->setPassword($hashedPassword);
        $apiKey = bin2hex(openssl_random_pseudo_bytes(16));

        $user
            ->setEmail("zhen.yang@hotmail.fr")
            ->setRoles(["ROLE_USER"])
            ->setApiKey($apiKey)
        ;

        $manager->persist($user);
        $manager->flush();
    }
}