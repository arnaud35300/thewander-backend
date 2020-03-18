<?php

namespace App\Security\Voter;

use App\Entity\Comment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CommentVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {    
        return in_array($attribute, ['COMMENT_UPDATE', 'COMMENT_DELETE'])
            && $subject instanceof Comment;
    }

    protected function voteOnAttribute($attribute, $comment, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'COMMENT_UPDATE':
                if ($user === $comment->getUser())
                    return true;
                break;
                
            case 'COMMENT_DELETE':
                if ($user === $comment->getUser())
                    return true;

                if ($this->security->isGranted('ROLE_MODERATOR'))
                    return true;
                break;
        }

        return false;
    }
}
