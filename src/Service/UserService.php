<?php
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function createUser(array $data): User
    {
        $user = new User();
        $user->setEmail($data['email']);
        $user->setCreatedAt(new \DateTime());
        
        // Hash password
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);
        
        // Set roles if provided
        if (isset($data['roles'])) {
            $user->setRoles((array) $data['roles']);
        }

        // Validate entity
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
