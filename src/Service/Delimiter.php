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

    public function verifyMargins(int $newX, int $newY): bool
    {
        $celestialBodies = $this->celestialBodyRepository->findAll();

        foreach ($celestialBodies as $celestialBody) {
            $xPosition = $celestialBody->getXPosition();
            $yPosition = $celestialBody->getYPosition();

            $minX = $xPosition;
            $maxX = $xPosition + 200;
            $minY = $yPosition;
            $maxY = $yPosition + 200;

            if (($newX >= $minX) || ($newX <= $maxX) && ($newY >= $minY) || ($newY <= $maxY)) 
                return false;
        }

        return true;
    }
}