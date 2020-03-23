<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\CelestialBody;
use Swift_Mailer as SwiftMailer;
use App\Repository\UserRepository;
use App\Repository\RoleRepository;
use App\Repository\CommentRepository;
use App\Repository\CelestialBodyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        $celestialBodies = $celestialBodyRepository->findBy(
            array(),
            ['updatedAt' => 'DESC']
        );

        return $this->render(
            'interface/celestialbodies.html.twig',
            ['celestialBodies' => $celestialBodies]
        );
    }

    /**
     *? Retrieves a particular celestial body.
     * 
     * @param CelestialBody $celestialBody The CelestialBody entity.
     * 
     * @return Response
     * 
     ** @IsGranted("ROLE_MODERATOR", statusCode=404)
     * 
     ** @Route("/celestial-bodies/{slug}", name="celestial_body", methods={"GET"})
     */
    public function getCelestialBody(CelestialBody $celestialBody = null): Response
    {
        if ($celestialBody === null) {
            $this->addFlash(
                'failure', 
                'Celestialbody not found.'
            );

            return $this->redirectToRoute('admin_celestial_bodies_list');
        }

        return $this->render(
            'interface/celestialbody.html.twig',
            ['celestialBody' => $celestialBody]
        );
    }

    /**
     *? Deletes a particular celestial body.
     * 
     * @param CelestialBody $celestialBody The CelestialBody entity.
     * 
     * @return Response
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/celestial-bodies/{slug}", name="delete_celestial_body", methods={"DELETE"})
     */
    public function deleteCelestialBody(CelestialBody $celestialBody = null): Response
    {        
        if ($celestialBody === null) {
            $this->addFlash(
                'failure',
                'CelestialBody not found.'
            );

            return $this->redirectToRoute('admin_celestial_bodies_list');
        }
        
        if ($celestialBody->getPicture() !== null)
            unlink(__DIR__ . '/../../public/assets/images/pictures/' . $celestialBody->getPicture());

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->remove($celestialBody);
        $manager->flush();

        return $this->redirectToRoute('admin_celestial_bodies_list');
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
        $users = $userRepository->findBy(
            array(),
            ['updatedAt' => 'DESC']
        );

        return $this->render(
            'interface/users.html.twig',
            ['users' => $users]
        );
    }

    /**
     *? Retrieves a particular user's information.
     *
     * @param User $user The user entity.
     * 
     * @return Response
     * 
     ** @IsGranted("ROLE_MODERATOR", statusCode=404)
     * 
     ** @Route("/users/{slug}", name="user", methods={"GET"})
     */
    public function getUser(User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash(
                'failure', 
                'User not found.'
            );

            return $this->redirectToRoute('admin_users_list');
        }

        return $this->render(
            'interface/user.html.twig',
            ['user' => $user]
        );
    }

    /**
     *? Toggles the user's status (ban or unban).
     * 
     * @param RoleRepository $roleRepository The Role repository.
     * @param User $user The User Repository.
     * @param SwiftMailer $mailer The SwiftMailer service.
     * 
     * @return Response
     *
     ** @IsGranted("ROLE_MODERATOR", statusCode=401) 
     *  
     ** @Route("/users/{slug}", name="toggle_user", methods={"PATCH"})
     */
    public function toggleUserStatus(RoleRepository $roleRepository, User $user = null, SwiftMailer $mailer): Response
    {
        if ($user === null) {
            $this->addFlash(
                'failure', 
                'User not found.'
            );

            return $this->redirectToRoute('admin_users_list');
        }

        switch ($user->getStatus()) {
            case 0:
                $user->setStatus(1);
                break;
            
            case 1:
                $user->setStatus(0);
                
                $newRole = $roleRepository->findOneByName('ROLE_CONTRIBUTOR');
                $user->setRole($newRole);

                $comments = $user->getComments();
                
                foreach ($comments as $comment)
                    $user->removeComment($comment);

                $celestialBodies = $user->getCelestialBodies();

                foreach ($celestialBodies as $celestialBody) 
                    $user->removeCelestialBody($celestialBody);

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

        return $this->redirectToRoute('admin_users_list');
    }

    /**
     *? Deletes a user account.
     * 
     * @param User $user The user entity.
     * 
     * @return Response
     *
     ** @IsGranted("ROLE_ADMINISTRATOR", statusCode=401)
     *  
     ** @Route("/users/{slug}", name="delete_user", methods={"DELETE"})
     */
    public function deleteUser(User $user = null): Response
    {
        if ($user === null) {
            $this->addFlash(
                'failure',
                'User not found.'
            );

            return $this->redirectToRoute('admin_users_list');
        }

        if (preg_match('#0[0-4]_avatar\.png#', $user->getAvatar()) !== 1)
            unlink(__DIR__ . '/../../public/assets/images/avatars/' . $user->getAvatar());

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('admin_users_list');
    }

    /**
     *? Retrieves all comments by the last one created or updated.
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
        $comments = $commentRepository->findBy(
            array(),
            ['updatedAt' => 'DESC']
        );

        return $this->render(
            'interface/comments.html.twig',
            ['comments' => $comments]
        );
    }

    /**
     *? Deletes a user's comment.
     * 
     * @param Comment $comment The Comment entity.
     * 
     * @return Response
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/comments/{id}", name="delete_comment", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function deleteComment(Comment $comment = null): Response
    {
        if ($comment === null) {
            $this->addFlash(
                'failure',
                'Comment not found.'
            );

            return $this->redirectToRoute('admin_comments_list');
        }

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute('admin_comments_list');
    }
}