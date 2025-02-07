<?php
namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:add-user',
    description: 'Creates a new user.',
    hidden: false,
)]
class AddUserCommand extends Command
{
    protected static $defaultName = 'app:add-user';
    private $entityManager;
    private $passwordHasher;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Adds a user to the database.')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'User email address')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'User password')
            ->addOption('role', null, InputOption::VALUE_OPTIONAL, 'User role', 'ROLE_USER');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $role = $input->getOption('role');

        // Check if user already exists
        if ($this->userRepository->findOneBy(['email' => $email])) {
            $output->writeln('<error>Email is already registered!</error>');
            return Command::FAILURE;
        }

        // Create the new user
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$role]);

        // Hash the password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setCreatedAt(new \DateTime());

        // Persist and flush the user entity
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('<info>User added successfully!</info>');

        return Command::SUCCESS;
    }
}
