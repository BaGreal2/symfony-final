<?php
namespace App\Controller;

use App\Service\ApiFormatter;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/users')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserService $userService,
        private readonly SerializerInterface $serializer
    ) {
    }

    #[Route('', name: 'users_list', methods: ['GET'])]
    public function list(ApiFormatter $apiFormatter): JsonResponse
    {
        $users = $this->userRepository->findAllWithQueryBuilder();

        return $this->json($apiFormatter->format($users), 200);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show($id, ApiFormatter $apiFormatter): JsonResponse
    {
        $id = (int) $id;
        $user = $this->userRepository->findWithDetails($id);
        
        if (!$user) {
          return $this->json($apiFormatter->format(null, 'error', 'User not found'), 404);
        }

        $data = $this->formatUserData($user);

        return $this->json($apiFormatter->format($data), 200);
    }

    #[Route('', name: 'user_create', methods: ['POST'])]
    public function create(Request $request, ApiFormatter $apiFormatter): JsonResponse
    {
        try {
            /** @var array $data */
            $data = $this->serializer->decode($request->getContent(), 'json');
            
            $user = $this->userService->createUser($data);
            
            $userData = $this->formatUserData($user);

            return $this->json($apiFormatter->format($userData), 201);
            
        } catch (\InvalidArgumentException $e) {
          return $this->json($apiFormatter->format(null, 'error', json_decode($e->getMessage())), 500);
        } catch (\Exception $e) {
          return $this->json($apiFormatter->format(null, 'error', 'Server error'), 500);
        }
    }

    private function formatUserData(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'created_at' => $user->getCreatedAt()->format(\DateTimeInterface::ATOM)
        ];
    }
}
