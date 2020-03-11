<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CommentEvent
{
    private $token;

    public function __construct(TokenInterface $token)
    {
        $this->token = $token;
    }

    public function prePersist(Comment $comment, User $user)
    {
        $comment->setUser(
            $this->token->getUser()
        );

        $user->setExperience(
            $user->getExperience()++
        );
    }

    public function preUpdate(Comment $comment)
    {
        $comment->setUpdatedAt(new \DateTime());
    }
}