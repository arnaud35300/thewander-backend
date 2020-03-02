<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/users", name="api_")
 */
class UserController extends AbstractController
{
    /**
     * Retrieves all users
     *
     * @Route(name="users_list", methods={"GET"})
     *
     * @param UserRepository $userRepository
     * 
     * @return JsonResponse
     */
    public function getAll(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findall();

        return $this->json(
            $users,
            Response::HTTP_OK,
            array(),
            ['groups' => 'users_list']
        );
    }

    /**
     * Retrieves a partical user
     *
     * @Routes("/{slug}", name="user", methods={"GET"})
     * 
     * @param User $user
     * @return JsonResponse
     */
    public function getOne(User $user = null): JsonResponse
    {
        if (!$user) {
            return $this->json(
                ['error' => 'user not found.'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $user,
            Response::HTTP_OK,
            array(),
            ['groups' => 'users_list']
        );
    }
}
