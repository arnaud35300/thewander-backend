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

        switch ($user->getStatus()) {
            case 1:
                $user->setStatus(0);
                $message = 'User now banned.';
                break;
            
            case 0:
                $user->setStatus(1);
                $message = 'User now unbanned.';
                break;
        }

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->persist($user);
        $manager->flush();

        return $this->json(
            ['message' => $message],
            Response::HTTP_OK
        );
    }
}
