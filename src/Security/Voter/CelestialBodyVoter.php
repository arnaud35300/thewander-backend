<?php

namespace App\Security\Voter;

use App\Entity\CelestialBody;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CelestialBodyVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {    
        return in_array($attribute, ['CELESTIAL_BODY_UPDATE', 'CELESTIAL_BODY_DELETE'])
            && $subject instanceof CelestialBody;
    }

    protected function voteOnAttribute($attribute, $celestialBody, TokenInterface $token)
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface)
            return false;

        switch ($attribute) {
            case 'CELESTIAL_BODY_UPDATE':
                if ($user === $celestialBody->getUser())
                    return true;
                break;
                
            case 'CELESTIAL_BODY_DELETE':
                if ($user === $celestialBody->getUser())
                    return true;

                if ($this->security->isGranted('ROLE_MODERATOR'))
                    return true;
                break;
        }

        return false;
    }
}





