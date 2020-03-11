<?php

namespace App\EventListener;

use App\Entity\User;
use App\Service\Slugger;
use App\Entity\CelestialBody;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CelestialBodyEvent
{
    private $slugger;
    private $token;

    public function __construct(Slugger $slugger, TokenInterface $token)
    {
        $this->slugger = $slugger;
        $this->token = $token;
    }

    public function prePersist(CelestialBody $celestialBody, User $user)
    {
        $celestialBody->setUser(
            $this->token->getUser()
        );
        
        $user->setExperience(
            $user->getExperience() + 5
        );

        $celestialBody->setSlug(
            $this->slugger->slugify(
                $celestialBody->getName()
            )
        );        
    }

    public function preUpdate(CelestialBody $celestialBody)
    {
        $celestialBody->setSlug(
            $this->slugger->slugify(
                $celestialBody->getName()
            )
        );
    }
}