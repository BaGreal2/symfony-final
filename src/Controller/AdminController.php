<?php

namespace App\Controller;

use App\Service\ApiFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminController extends AbstractController
{
    /**
     * A route accessible only to admin users.
     */
    #[Route('/api/admin/dashboard', name: 'admin_dashboard', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(ApiFormatter $apiFormatter): JsonResponse
    {
        // For demo, simply return a welcome message.
        return $this->json($apiFormatter->format(['message' => 'Welcome, admin!']));
    }
}
