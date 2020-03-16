<?php

namespace App\Service;

use App\Repository\CelestialBodyRepository;

class Delimiter
{
    const WIDTH = 210;

    private $celestialBodyRepository;

    public function __construct(CelestialBodyRepository $celestialBodyRepository)
    {
        $this->celestialBodyRepository = $celestialBodyRepository;
    }

    public function verifyPositions(int $newX, int $newY, ?string $slug = ''): bool
    {
        $celestialBodies = $this->celestialBodyRepository->getAllExceptCurrent($slug);
      
        $newMinX = $newX;
        $newMaxX = $newX + self::WIDTH;
        $newMinY = $newY;
        $newMaxY = $newY + self::WIDTH;

        foreach ($celestialBodies as $celestialBody) {
            $xPosition = $celestialBody->getXPosition();
            $yPosition = $celestialBody->getYPosition();

            $minX = $xPosition;
            $maxX = $xPosition + self::WIDTH;
            $minY = $yPosition;
            $maxY = $yPosition + self::WIDTH;

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

            if ($yResult === false && $xResult === false)
                return false;
        }
        return true;
    }
}
