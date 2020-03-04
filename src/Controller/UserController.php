<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/users", name="api_")
 */
class UserController extends AbstractController
{
    /**
     *? Retrieves all users.
     *
     * @param UserRepository $userRepository The User repository.
     * 
     * @return JsonResponse
     *
     ** @Route(name="users_list", methods={"GET"})
     */
    public function getAll(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        return $this->json(
            $users,
            Response::HTTP_OK,
            array(),
            ['groups' => 'users']
        );
    }

    /**
     *? Retrieves a partical user.
     *
     * @param User $user The user entity.
     * 
     * @return JsonResponse
     * 
     ** @Route("/{slug}", name="user", methods={"GET"})
     */
    public function getOne(User $user = null): JsonResponse
    {
        if ($user === null || $user->getStatus() === 0) {
            return $this->json(
                ['error' => 'user not found.'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $user,
            Response::HTTP_OK,
            array(),
            ['groups' => 'user']
        );
    }

    /**
     *? Create a new user.
     *
     * @param Request $request The HttpFoundation Request class.
     * @param SerializerInterface $serializer The Serializer component.
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $encoder
     * 
     * @return JsonResponse
     * 
     ** @Route(name="create_user", methods={"POST"})
     */
    public function create(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $encoder
    ) {

        // TODO : authentication requirements

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
            ['groups' => 'user-creation']
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

        // TODO make event for this
        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));


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
            Response::HTTP_CREATED,
            array(),
            ['groups' => 'user']
        );
    }

    /**
     *? Update a user.
     *
     * @param Request $request The HttpFoundation Request class.
     * @param SerializerInterface $serializer The Serializer component.
     * @param ValidatorInterface $validator The Validator component.
     * @param UserPasswordEncoderInterface $encoder The PasswordEncoder component.
     * @param User $user The user entity.
     * 
     * @return JsonResponse
     * 
     ** @Route("/{slug}", name="update_user", methods={"PATCH"})
     */
    public function update(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $encoder,
        User $user = null
    ) {

        // TODO : authentication requirements

        if ($user === null) {
            return $this->json(
                ['error' => 'user not found.'],
                Response::HTTP_NOT_FOUND
            );
        }

        $content = $request->getContent();

        if (json_decode($content) === null) {
            return $this->json(
                ['error' => 'invalid data format'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $content = json_decode($content, true);

        $password = !empty($content['password']) ? $content['password'] : $user->getPassword();
        $avatar = !empty($content['avatar']) ? $content['avatar'] : $user->getAvatar();
        $firstname = !empty($content['firstname']) ? $content['firstname'] : $user->getFirstname();
        $birthday = !empty($content['birthday']) ? $content['birthday'] : $user->getBirthday();
        $bio = !empty($content['bio']) ? $content['bio'] : $user->getBio();

        $user
            ->setPassword($encoder->encodePassword($user, $password))
            ->setAvatar($avatar)
            ->setFirstname($firstname)
            ->setBirthday($birthday)
            ->setBio($bio)
        ;

        $errors = $validator->validate($user);
        if (count($errors) !== 0) {
            $errorsList = array();

            foreach ($errors as $error) {
                $errorsList[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
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

        $user = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'user-update']
        );

        return $this->json(
            [
                'message' => 'user updated.',
                'content' => $user
            ],
            Response::HTTP_OK
        );
    }

    /**
     *? Deletes a user.
     * 
     * @param User $user The user entity.
     * 
     * @return JsonResponse
     * 
     ** @Route("/{slug}", name="delete_user", methods={"DELETE"})
     */
    public function delete(User $user = null): JsonResponse
    {
        // TODO : authentication requirements

        if ($user === null) {
            return $this->json(
                ['error' => 'this user does not exist.'],
                Response::HTTP_NOT_FOUND
            );
        }

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->remove($user);
        $manager->flush();

        return $this->json(
            ['message' => 'user deleted.'],
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     *? Retrieves all user's celestialbodies.
     *
     * @param User $user The user entity.
     * 
     * @return JsonResponse
     * 
     ** @Route("/{slug}/celestial-bodies", name="api_user_celestial_bodies", methods={"GET"})
     */
    public function getCelestialBodies(User $user = null): JsonResponse
    {
        if ($user === null || $user->getStatus() === 0) {
            return $this->json(
                ['error' => 'user not found.'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $user,
            Response::HTTP_OK,
            array(),
            ['groups' => 'user-celestial-bodies']
        );

    }
}
