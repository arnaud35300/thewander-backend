<?php

namespace App\EventListener;

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

    /**
     *? Sets some of the entity's properties automatically once a new celestial body is instantiated.
     * 
     * @param CelestialBody $celestialBody The CelestialBody entity.
     * 
     * @return void
     */
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

    /**
     *? Sets some of the entity's properties automatically once a celestial body is updated.
     *
     * @param CelestialBody $celestialBody The CelestialBody entity.
     * 
     * @return void
     */
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