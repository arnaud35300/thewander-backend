<?php

namespace App\Controller;

use App\Entity\User;
use Swift_Mailer as SwiftMailer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @param SwiftMailer $mailer The SwiftMailer service.
     * 
     * @return JsonResponse
     * 
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