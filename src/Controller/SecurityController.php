<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * name="api_"
 */
class SecurityController extends AbstractController
{
    /**
     *? Connects a user.
     * 
     * @return JsonResponse
     * 
     ** @Route("/login", name="login", methods={"GET", "POST"})
     */
    public function login(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
    }
}