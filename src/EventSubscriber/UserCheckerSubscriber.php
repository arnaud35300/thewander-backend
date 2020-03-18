<?php

namespace App\EventSubscriber;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserCheckerSubscriber implements EventSubscriberInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     *? Checks the user's status before logging in.
     *
     *? If the status is set to 0 the user will be prevented from logging in.
     *? If the status is set to 1 the user will be logged in.
     * 
     * @param ResponseEvent $event The HttpKernel response event component.
     * 
     * @return void
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        if ($event->getRequest()->getRequestUri() === "/login") {
            $data = json_decode($event->getRequest()->getContent(), true);
            
            $username = isset($data['username']) ? $data['username'] : false;

            if ($username) {
                $user = $this->userRepository->findOneByEmail($username);

                if ($user && $user->getStatus() === 0) {
                    $response = $event->getResponse();

                    $error = [
                        'message' => 'This account is banned.'
                    ];

                    $error = json_encode($error);

                    $response
                        ->setContent($error)
                        ->setStatusCode(Response::HTTP_FORBIDDEN)
                    ;
                }
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }
}
