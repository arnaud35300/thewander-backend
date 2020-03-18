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

    /**
     *? Sets some of the entity's properties automatically once a new comment is instantiated.
     * 
     * @param Comment $comment The Comment entity.
     * 
     * @return void
     */
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

    /**
     *? Sets some of the entity's properties automatically once a comment is updated.
     *
     * @param Comment $comment The Comment entity.
     * 
     * @return void
     */
    public function preUpdate(Comment $comment)
    {
        $comment->setUpdatedAt(new \DateTime());
    }
}