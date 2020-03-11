<?php

namespace App\EventListener;

use App\Entity\User;
use App\Service\Slugger;
use App\Entity\CelestialBody;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface as StorageTokenStorage;

class CelestialBodyEvent
{
    private $slugger;
    private $token;

    public function __construct(Slugger $slugger, StorageTokenStorage $token)
    {
        $this->slugger = $slugger;
        $this->token = $token;
    }

    public function prePersist(CelestialBody $celestialBody)
    {
        $user = $this->token->getToken()->getUser();

        $celestialBody->setUser(
            $user
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
        $celestialBody->setUpdatedAt(new \DateTime());

        $celestialBody->setSlug(
            $this->slugger->slugify(
                $celestialBody->getName()
            )
        );
    }
}