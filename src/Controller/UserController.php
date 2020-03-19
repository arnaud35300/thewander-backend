<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Censor;
use App\Service\Uploader;
use Swift_Mailer as SwiftMailer;
use App\Repository\UserRepository;
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
     *? Retrieves all the users.
     *
     * @param UserRepository $userRepository The User repository.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_MODERATOR", statusCode=404)
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
     *? Retrieves a particular user's information.
     *
     * @param User $user The user entity.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/users/{slug}", name="user", methods={"GET"})
     */
    public function getOne(User $user = null): JsonResponse
    {
        if ($user === null || $user->getStatus() === 0)
            return $this->json(
                ['information' => 'User not found.'],
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
     *? Retrieves the connected user's information.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/self", name="current_user", methods={"GET"})
     */
    public function getSelf(): JsonResponse
    {
        $user = $this->getUser();

        if ($user === null || $user->getStatus() === 0)
            return $this->json(
                ['information' => 'Log in to check your information.'],
                Response::HTTP_UNAUTHORIZED
            );

        return $this->json(
            $user,
            Response::HTTP_OK,
            array(),
            ['groups' => 'current-user']
        );
    }

    /**
     *? Create a new user account.
     *
     * @param Request $request The HttpFoundation Request class.
     * @param SerializerInterface $serializer The Serializer component.
     * @param ValidatorInterface $validator The Validator component.
     * @param Censor $censor The Censor service.
     * @param SwiftMailer $mailer The SwiftMailer service.
     * 
     * @return JsonResponse
     * 
     ** @Route("/users", name="create_user", methods={"POST"})
     */
    public function create(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Censor $censor,
        SwiftMailer $mailer
    ): JsonResponse
    {
        $content = $request->getContent();

        if (json_decode($content) === null)
            return $this->json(
                ['information' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );

        if ($censor->check($content) === false)
            return $this->json(
                ['information' => 'Bad words are forbidden.'],
                Response::HTTP_UNAUTHORIZED
            );

        $user = $serializer->deserialize(
            $content,
            User::class,
            'json',
            ['groups' => 'user-creation']
        );

        $violations = $validator->validate($user);

        if ($violations->count() > 0)
            return $this->json(
                $violations,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        $manager = $this
            ->getDoctrine()
            ->getManager();

        $manager->persist($user);
        $manager->flush();

        $message = (new \Swift_Message('Ready to browse the space!'))
            ->setFrom('thewandercorp@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/signup.html.twig',
                        [
                            'user' => $user
                        ]
                ),
                'text/html'
        );

        $mailer->send($message);

        return $this->json(
            [
                'information' => 'Account now created.',
                'content' => $user
            ],
            Response::HTTP_CREATED,
            array(),
            ['groups' => 'user']
        );
    }

    /**
     *? Updates a user's information.
     *
     * @param Request $request The HttpFoundation Request class.
     * @param UserPasswordEncoderInterface $encoder The PasswordEncoder component.
     * @param ValidatorInterface $validator The Validator component.
     * @param SerializerInterface $serializer The Serializer component.
     * @param Censor $censor The Censor service.
     * @param Uploader $uploader The Uploader service.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/self/update", name="update_user", methods={"POST"})
     */
    public function update(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        Censor $censor,
        Uploader $uploader
    ): JsonResponse
    {
        $request->setMethod('PATCH');

        $user = $this->getUser();

        if ($user === null)
            return $this->json(
                ['information' => 'Log in to edit your information.'],
                Response::HTTP_UNAUTHORIZED
            );

        $content = $request->request->get('json');

        if (json_decode($content) === null)
            return $this->json(
                ['information' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );
        
        if ($censor->check($content) === false)
            return $this->json(
                ['information' => 'Bad words are forbidden.'],
                Response::HTTP_UNAUTHORIZED
            );

        $content = json_decode($content, true);

        $password = !empty($content['password']) ? $content['password'] : false;
        $firstname = !empty($content['firstname']) ? $content['firstname'] : null;
        $birthday = !empty($content['birthday']) ? $content['birthday'] : false;
        $bio = !empty($content['bio']) ? $content['bio'] : $user->getBio();
        
        if ($password) 
            $user->setPassword(
                $encoder->encodePassword($user, $password)
            );

        $user
            ->setFirstname($firstname)
            ->setBio($bio)
        ;
        
        $violations = $validator->validate($user);

        if ($violations->count() > 0)
            return $this->json(
                $violations,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        $pattern = '#\d{4}-\d{2}-\d{2}#';

        if($birthday) {
            if (preg_match($pattern, $birthday) !== 1)
                return $this->json(
                    ['information' => 'Invalid date format.'],
                    Response::HTTP_UNAUTHORIZED
                );
            
            $birthday = \DateTime::createFromFormat('Y-m-d', $birthday);
            
            $user->setBirthday($birthday);
        }
        
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
                    ['information' => $avatar],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );

            $avatarDirectory = __DIR__ . '/../../public/images/avatars/';
        
            if ($user->getAvatar() !== null)
                unlink($avatarDirectory . $user->getAvatar());
            
            $user->setAvatar($avatar['avatar']);
        }

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->persist($user);
        $manager->flush();

        $user = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'user']
        );

        return $this->json(
            [
                'information' => 'Information now updated.',
                'content' => $user
            ],
            Response::HTTP_OK
        );
    }

    /**
     *? Updates the user's preference.
     *
     * @param Request $request The HttpFoundation Request class.
     * @param ValidatorInterface $validator The Validator component.
     * @param SerializerInterface $serializer The Serializer component.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/self/preference", name="update_user_preferences", methods={"PATCH"})
     */
    public function updatePreference(
        Request $request,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $user = $this->getUser();

        if ($user === null)
            return $this->json(
                ['information' => 'Log in to edit your preferences.'],
                Response::HTTP_UNAUTHORIZED
            );

        $content = $request->getContent();
        $preference = $user->getPreference();

        if (json_decode($content) === null)
            return $this->json(
                ['information' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );

        $content = json_decode($content, true);

        $volume = isset($content['volume']) ? $content['volume'] : $preference->getVolume();
        $soundscape = isset($content['soundscape']) ? $content['soundscape'] : $preference->getSoundscape();

        $preference
            ->setVolume($volume)
            ->setSoundscape($soundscape)
        ;

        $violations = $validator->validate($user);

        if ($violations->count() > 0)
            return $this->json(
                $violations,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );       

        $manager = $this
            ->getDoctrine()
            ->getManager();

        $manager->persist($preference);
        $manager->flush();

        $preference = $serializer->serialize(
            $preference,
            'json',
            ['groups' => 'user-preference-update']
        );

        return $this->json(
            [
                'information' => 'Preferences now updated.',
                'content' => $preference
            ],
            Response::HTTP_OK
        ); 
    }

    /**
     *? Deletes a user account.
     * 
     * @param User $user The user entity.
     * 
     * @return JsonResponse
     *
     ** @IsGranted("ROLE_ADMINISTRATOR", statusCode=401)
     *  
     ** @Route("/users/{slug}", name="delete_user", methods={"DELETE"})
     */
    public function delete(User $user = null): JsonResponse
    {
        if ($user === null || $user->getStatus() === 0)
            return $this->json(
                ['information' => 'User not found.'],
                Response::HTTP_NOT_FOUND
            );

        if ($user->getAvatar())
            unlink(__DIR__ . '/../../public/images/avatars/' . $user->getAvatar());

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->remove($user);
        $manager->flush();

        return $this->json(
            ['information' => 'User now deleted.'],
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     *? Deletes the user's account.
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
                ['information' => 'Log in to perform deletion.'],
                Response::HTTP_UNAUTHORIZED
            );

        if ($user->getAvatar())
            unlink(__DIR__ . '/../../public/images/avatars/' . $user->getAvatar());

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->remove($user);
        $manager->flush();

        return $this->json(
            ['information' => 'Account now deleted.'],
            Response::HTTP_NO_CONTENT
        );
    }
}