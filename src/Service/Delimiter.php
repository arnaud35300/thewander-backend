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

    public function verifyPositions(int $newX, int $newY): bool
    {
        $celestialBodies = $this->celestialBodyRepository->findAll();

        foreach ($celestialBodies as $celestialBody) {
            $xPosition = $celestialBody->getXPosition();
            $yPosition = $celestialBody->getYPosition();

            $minX = $xPosition;
            $maxX = $xPosition + 200;
            $minY = $yPosition;
            $maxY = $yPosition + 200;

            $XG = $newX; // gauche
            $YH = $newY; // haut
            $XD = $newX + 200; // droit
            $YB = $newY + 200; // bas

            $Xresult = false;
            $Yresult = false;

            // Si gauche est supếrieur a minX et inférieur a maxX
            if ($XG >= $minX && $XG <= $maxX) {
                $Xresult = true;
            }

            // Si droite est supếrieur a minX et inférieur a maxX
            if ($XD >= $minX && $XD <= $maxX) {
                $Xresult = true;
            }

            // Si haut est supếrieur a minY et inférieur a maxY
            if ($YH >= $minY && $YH <= $maxY) {
                $Yresult = true;
            }

            // Si bas est supếrieur a minY et inférieur a maxY
            if ($YB >= $minY && $YB <= $maxY) {
                $Yresult = true;
            }
        }

        //! REFAIRE AVEC X DE ET Y DE BASE AVEC - 200 | + 200 -> DIRECT SUR Y ET X CHAMP DE LETOILE COMPARE

        $result = $Yresult && $Xresult;
    }
}
