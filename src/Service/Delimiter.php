<?php

namespace App\Service;

use App\Repository\CelestialBodyRepository;

class Delimiter
{
    private $celestialBodyRepository;

    public function __construct(CelestialBodyRepository $celestialBodyRepository)
    {
        $this->celestialBodyRepository = $celestialBodyRepository;
    }

    public function verifyMargins()
    {
        $celestialBodies = $this->celestialBodyRepository->findAll();

        foreach ($celestialBodies as $celestialBody) {
            $xPosition = $celestialBody->getXPosition();
            $yPosition = $celestialBody->getYPosition();
        }
    }
}