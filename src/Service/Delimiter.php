<?php

namespace App\Service;

use App\Repository\CelestialBodyRepository;

class Delimiter
{
    const GAP = 210;
    private $celestialBodyRepository;

    public function __construct(CelestialBodyRepository $celestialBodyRepository)
    {
        $this->celestialBodyRepository = $celestialBodyRepository;
    }

    public function verifyPositions(int $newX, int $newY): bool
    {
        $celestialBodies = $this->celestialBodyRepository->findAll();

        $newMinX = $newX;
        $newMaxX = $newX + self::GAP;
        $newMinY = $newY;
        $newMaxY = $newY + self::GAP;

        foreach ($celestialBodies as $celestialBody) {
            $xPosition = $celestialBody->getXPosition();
            $yPosition = $celestialBody->getYPosition();

            $minX = $xPosition;
            $maxX = $xPosition + self::GAP;
            $minY = $yPosition;
            $maxY = $yPosition + self::GAP;

            $xResult = true;
            $yResult = true;

            if (
                ($newMinX >= $minX && $newMinX <= $maxX) ||
                ($newMaxX >= $minX && $newMaxX <= $maxX)
            )
                $xResult = false;

            if (
                ($newMinY >= $minY && $newMinY <= $maxY) ||
                ($newMaxY >= $minY && $newMaxY <= $maxY)
            )
                $yResult = false;
        }

        return $xResult && $yResult;
    }
}