<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/users", name="api_")
 */
class UserController extends AbstractController
{
    /**
     * Retrieves all users
     *
     * @param UserRepository $userRepository
     * 
     * @return JsonResponse
     *
     *  @Route(name="users_list", methods={"GET"})
     */
    public function getAll(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

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
     * @param User $user
     * 
     * @return JsonResponse
     * 
     * @Route("/{slug}", name="user", methods={"GET"})
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

    /**
     * Create a new user
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * 
     * @return JsonResponse
     * 
     * @Route(name="create_user", methods={"POST"})
     */
    public function create(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {

        //TODO make voters

        $content = $request->getContent();

        if (json_decode($content) === null) {
            return $this->json(
                ['error' => 'invalid data format'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $user = $serializer->deserialize(
            $content,
            User::class,
            'json',
            ['groups' => 'user_create']
        );

        $errors = $validator->validate($user);

        if (count($errors) !== 0) {
            $errorsList = array();

            foreach ($errors as $error) {
                $errorsList[] = [
                    'field'     => $error->getPropertyPath(),
                    'message'   => $error->getMessage()
                ];
            }

            return $this->json(
                $errorsList,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $manager = $this
            ->getDoctrine()
            ->getManager();

        $manager->persist($user);
        $manager->flush();

        return $this->json(
            [
                'message' => 'user created',
                'content' => $user
            ],
            Response::HTTP_CREATED
        );
    }
}
