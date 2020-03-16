<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Slugger;
use App\Repository\UserRepository;
use App\Service\Uploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route(name="api_")
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
     ** @Route("/users", name="users_list", methods={"GET"})
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
     ** @Route("/users/{slug}", name="user", methods={"GET"})
     */
    public function getOne(User $user = null): JsonResponse
    {
        if ($user === null || $user->getStatus() === 0)
            return $this->json(
                ['message' => 'User not found.'],
                Response::HTTP_NOT_FOUND
            );

        return $this->json(
            $user,
            Response::HTTP_OK,
            array(),
            ['groups' => 'user']
        );
    }

    /**
     *? Retrieves the connected user.
     * 
     * @return JsonResponse
     * 
     ** @Route("/self", name="current_user", methods={"GET"})
     */
    public function getSelf(): JsonResponse
    {
        $user = $this->getUser();

        if ($user === null || $user->getStatus() === 0)
            return $this->json(
                ['message' => 'User not found.'],
                Response::HTTP_NOT_FOUND
            );

        return $this->json(
            $user,
            Response::HTTP_OK,
            array(),
            ['groups' => 'current-user']
        );
    }

    /**
     *? Retrieves all user's celestialbodies.
     *
     * @param User $user The user entity.
     * 
     * @return JsonResponse
     * 
     ** @Route("/users/{slug}/celestial-bodies", name="api_user_celestial_bodies", methods={"GET"})
     */
    public function getCelestialBodies(User $user = null): JsonResponse
    {
        if ($user === null || $user->getStatus() === 0)
            return $this->json(
                ['message' => 'User not found.'],
                Response::HTTP_NOT_FOUND
            );

        return $this->json(
            $user,
            Response::HTTP_OK,
            array(),
            ['groups' => 'user-celestial-bodies']
        );
    }

    /**
     *? Create a new user.
     *
     * @param Request $request The HttpFoundation Request class.
     * @param SerializerInterface $serializer The Serializer component.
     * @param ValidatorInterface $validator The Validator component.
     * @param UserPasswordEncoderInterface $encoder The PasswordEncoder component.
     * @param Slugger $slugger The Slugger service.
     * 
     * @return JsonResponse
     * 
     ** @Route("/users", name="create_user", methods={"POST"})
     */
    public function create(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $encoder,
        Slugger $slugger
    ) {
        $content = $request->getContent();

        if (json_decode($content) === null)
            return $this->json(
                ['message' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );

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

        $manager = $this
            ->getDoctrine()
            ->getManager();

        $manager->persist($user);
        $manager->flush();

        return $this->json(
            [
                'message' => 'User created.',
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
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/self/update", name="update_user", methods={"POST"})
     */
    public function update(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $encoder,
        Uploader $uploader
    ) {
        $request->setMethod('PATCH');

        $user = $this->getUser();

        if ($user === null)
            return $this->json(
                ['message' => 'User not found.'],
                Response::HTTP_NOT_FOUND
            );

        $content = $request->request->get('json');

        if (json_decode($content) === null)
            return $this->json(
                ['message' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );

        $content = json_decode($content, true);

        $password = !empty($content['password']) ? $content['password'] : $user->getPassword();
        $firstname = !empty($content['firstname']) ? $content['firstname'] : false;
        $birthday = !empty($content['birthday']) ? $content['birthday'] : false;
        $bio = !empty($content['bio']) ? $content['bio'] : $user->getBio();
        
        $errors = $validator->validate($user);
       
        $user
            ->setPassword($encoder->encodePassword($user, $password))
            ->setFirstname($firstname)
            ->setBio($bio)
        ;
        
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

        $pattern = '#\d{4}-\d{2}-\d{2}#';

        if($birthday) {
            if (preg_match($pattern, $birthday) !== 1)
                return $this->json(
                    ['message' => 'Invalid birthday format.'],
                    Response::HTTP_UNAUTHORIZED
                );
            
            $birthday = \DateTime::createFromFormat('Y-m-d', $birthday);
            $user->setBirthday($birthday);
        }
        
        $avatarFolder = __DIR__ . '/../../public/images/avatars/';
        $userSlug = $user->getSlug();
        
        if ($request->files->get('avatar')) {
            $avatar = $uploader->upload(
                'avatars',
                $userSlug,
                '_avatar',
                'avatar',
                75
            );

            if ($avatar['status'] === false)
                return $this->json(
                    ['message' => $avatar],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
        
            if ($user->getAvatar() !== null)
                unlink($avatarFolder . $user->getAvatar());
            
            $user->setAvatar($avatar['avatar']);
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
                'message' => 'User updated.',
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
     ** @IsGranted("ROLE_ADMINISTRATOR", statusCode=404)
     *  
     ** @Route("/users/{slug}", name="delete_user", methods={"DELETE"})
     */
    public function delete(User $user = null): JsonResponse
    {
        if ($user === null || $user->getStatus() === 0)
            return $this->json(
                ['error' => 'This user does not exist.'],
                Response::HTTP_NOT_FOUND
            );

        $manager = $this
            ->getDoctrine()
            ->getManager();

        $manager->remove($user);
        $manager->flush();

        return $this->json(
            ['message' => 'User deleted.'],
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     *? Deletes the connected user.
     *  
     * @return JsonResponse
     *
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     *  
     ** @Route("/users", name="delete_self", methods={"DELETE"})
     */
    public function deleteSelf(): JsonResponse
    {
        $user = $this->getUser();

        if ($user === null)
            return $this->json(
                ['error' => 'This user does not exist.'],
                Response::HTTP_NOT_FOUND
            );

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->remove($user);
        $manager->flush();

        return $this->json(
            ['message' => 'User deleted.'],
            Response::HTTP_NO_CONTENT
        );
    }
}
