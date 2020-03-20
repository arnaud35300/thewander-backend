<?php

namespace App\Controller;

use App\Entity\User;
use Swift_Mailer as SwiftMailer;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\CelestialBodyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     *? Connects a member of the lead team to the back office's interface.
     * 
     * @param AuthenticationUtils $authenticationUtils The HTTP Authentication component.
     * 
     * @return Response 
     * 
     ** @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error
            ]
        );
    }

    /**
     *? Disconnects a member of the lead team to the back office's interface.
     *
     ** @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     *? Renders the back office's home page.
     * 
     * @return Response
     * 
     ** @IsGranted("ROLE_MODERATOR", statusCode=404) 
     * 
     ** @Route("/head", name="head", methods={"GET"})
     */
    public function showHome(): Response
    {
        return $this->render('interface/home.html.twig');
    }

    /**
     *? Retrieves all celestial bodies by the last one created or updated.
     * 
     * @param CelestialBodyRepository $celestialBodyRepository The CelestialBody repository.
     * 
     * @return Response
     * 
     ** @IsGranted("ROLE_MODERATOR", statusCode=404) 
     * 
     ** @Route("/celestial-bodies", name="celestial_bodies_list", methods={"GET"})
     */
    public function getCelestialBodies(CelestialBodyRepository $celestialBodyRepository): Response
    {
        $celestialBodies = $celestialBodyRepository->findByUpdatedAt();

        return $this->render(
            'interface/celestialbodies.html.twig',
            ['celestialBodies' => $celestialBodies]
        );
    }

    /**
     *? Retrieves all celestial bodies by the last one created or updated.
     * 
     * @param UserRepository $userRepository The User repository.
     * 
     * @return Response
     * 
     ** @IsGranted("ROLE_MODERATOR", statusCode=404) 
     * 
     ** @Route("/users", name="users_list", methods={"GET"})
     */
    public function getUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findByUpdatedAt();

        return $this->render(
            'interface/users.html.twig',
            ['users' => $users]
        );
    }

    /**
     *? Retrieves all celestial bodies by the last one created or updated.
     * 
     * @param CommentRepository $commentRepository The Comment repository.
     * 
     * @return Response
     * 
     ** @IsGranted("ROLE_MODERATOR", statusCode=404)
     * 
     ** @Route("/comments", name="comments_list", methods={"GET"})
     */
    public function getComments(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findByUpdatedAt();

        return $this->render(
            'interface/comments.html.twig',
            ['comments' => $comments]
        );
    }

    /**
     *? Toggles the user's status (ban or unban).
     * 
     * @param User $user The User Repository.
     * @param SwiftMailer $mailer The SwiftMailer service.
     * 
     * @return JsonResponse
     *
     ** @IsGranted("ROLE_MODERATOR", statusCode=404) 
     *  
     ** @Route("/users/{slug}", name="toggle_user", methods={"PATCH"})
     */
    public function toggleUserStatus(User $user = null, SwiftMailer $mailer): JsonResponse
    {
        if ($user === null)
            return $this->json(
                ['information' => 'User not found.'],
                Response::HTTP_NOT_FOUND
            );

        switch ($user->getStatus()) {
            case 0:
                $user->setStatus(1);
                $message = 'User now unbanned.';
                break;
            
            case 1:
                $user->setStatus(0);
                $message = 'User now banned.';

                $mail = (new \Swift_Message('We have bad news'))
                    ->setFrom('thewandercorp@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/ban-notification.html.twig',
                            [
                                'user' => $user
                            ]
                        ),
                        'text/html'
                );

                $mailer->send($mail);
                
                break;
        }

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->persist($user);
        $manager->flush();

        return $this->json(
            ['information' => $message],
            Response::HTTP_OK
        );
    }
}