<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="api_")
 */
class AdminController extends AbstractController
{
    /**
     * ? Ban or unbans a user.
     * 
     * @param User $user
     * 
     * @return JsonResponse
     * 
     * * @Route("/users/{slug}", name="toggle_user")
     */
    public function toggleUserStatus(User $user = null): JsonResponse
    {
        if ($user === null) {
            return $this->json(
                ['error' => 'user not found.'],
                Response::HTTP_NOT_FOUND
            );
        }

        if ($user->getStatus() === 1) {
            return $this->json(
                ['message' => 'user has been banned.'],
                Response::HTTP_OK
            );
        }

        if ($user->getStatus() === 0) {
            return $this->json(
                ['message' => 'user has been unbanned.'],
                Response::HTTP_OK
            );
        }        
    }
}
