<?php

namespace App\EventListener;

use App\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface as StorageTokenStorage;

class CommentEvent
{
    private $token;

    public function __construct(StorageTokenStorage $token)
    {
        $this->token = $token;
    }

    public function prePersist(Comment $comment)
    {
        $user = $this->token->getToken()->getUser();

        $comment->setUser(
            $user
        );

        $user->setExperience(
            $user->getExperience() + 1
        );
    }

    public function preUpdate(Comment $comment)
    {
        $comment->setUpdatedAt(new \DateTime());
    }
}