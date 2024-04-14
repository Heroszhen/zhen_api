<?php

namespace App\Controller\API\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UpdateUserPasswordController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function __invoke(User $data, Request $request): User
    {
        $content = json_decode($request->getContent(), true);
        if (
            !isset($content['password']) ||
            in_array($content['password'], ['', null])
        ) {
            throw new BadRequestHttpException('"password" is required');
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $data,
            $content['password']
        );
        $data->setPassword($hashedPassword);

        return $data;
    }
}