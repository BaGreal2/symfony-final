<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ApiFormatter;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * Registration endpoint.
     * Expects JSON with keys: email, password.
     */
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager,
        ApiFormatter $apiFormatter,
        UserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Check if user already exists
        if ($userRepository->findOneBy(['email' => $email])) {
            return $this->json(
                $apiFormatter->format(null, 'error', 'Email already registered'),
                400
            );
        }

        $user = new User();
        $user->setEmail($email);
        // Set a default role
        $user->setRoles(['ROLE_USER']);
        // Hash the password
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setCreatedAt(new \DateTime());

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($apiFormatter->format(['message' => 'User registered successfully'], 'success'), 201);
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(AuthenticationUtils $authenticationUtils): JsonResponse
    {
        return new JsonResponse(['error' => 'Login route should be handled by json_login.'], 400);
    }

    // /**
    //  * Login endpoint.
    //  * Expects JSON with keys: email, password.
    //  * (For demo purposes, this returns a fake token.)
    //  */
    // #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    // public function login(
    //     Request $request, 
    //     UserRepository $userRepository,
    //     UserPasswordHasherInterface $passwordHasher,
    //     ApiFormatter $apiFormatter
    // ): JsonResponse {
    //     $data = json_decode($request->getContent(), true);
    //     $email = $data['email'] ?? '';
    //     $password = $data['password'] ?? '';
    //
    //     $user = $userRepository->findOneBy(['email' => $email]);
    //     if (!$user) {
    //         return $this->json($apiFormatter->format(null, 'error', 'User not found'), 401);
    //     }
    //
    //     if (!$passwordHasher->isPasswordValid($user, $password)) {
    //         return $this->json($apiFormatter->format(null, 'error', 'Invalid credentials'), 401);
    //     }
    //
    //     // For demonstration, generate a dummy token.
    //     $token = md5($email . time());
    //
    //     return $this->json($apiFormatter->format(['token' => $token]));
    // }
}
