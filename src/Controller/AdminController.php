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
     *? Toggles the user's status (ban or unban).
     * 
     * @param User $user The User Repository.
     * 
     * @return JsonResponse
     * 
     ** @Route("/users/{slug}", name="toggle_user", methods={"PATCH"})
     */
    public function toggleUserStatus(User $user = null): JsonResponse
    {
        if ($user === null)
            return $this->json(
                ['error' => 'User not found.'],
                Response::HTTP_NOT_FOUND
            );

        if ($user->getStatus() === 1) {
            $user->setStatus(0);

            return $this->json(
                ['message' => 'User now banned.'],
                Response::HTTP_OK
            );
        }

        if ($user->getStatus() === 0) {
            $user->setStatus(1);

            return $this->json(
                ['message' => 'User now unbanned.'],
                Response::HTTP_OK
            );       
        }
    }
}
