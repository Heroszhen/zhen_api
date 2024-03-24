<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateRoleUserCommand extends Command
{
    protected static $defaultName = 'app:create-role-user';
    protected static $defaultDescription = 'Create one role user';

    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $manager;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $manager
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->manager = $manager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'E-mail')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $email = $input->getArgument('email');
        $found = $this->manager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($found instanceof User) {
            $io->error("'{$email}' is existing.");

            return Command::FAILURE;
        }


        $password = $input->getArgument('password');
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );
        $apiKey = bin2hex(openssl_random_pseudo_bytes(16));
        $user
            ->setEmail($email)
            ->setPassword($hashedPassword)
            ->setRoles(["ROLE_USER"])
            ->setApiKey($apiKey)
        ;

        $this->manager->persist($user);
        $this->manager->flush();

        $io->writeln("Email: {$email}");
        $io->writeln("apiKey: {$apiKey}");

        $io->success('Command is successfully executed.');

        return Command::SUCCESS;
    }
}
