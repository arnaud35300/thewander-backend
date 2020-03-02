<?php

namespace App\Controller;

use App\Entity\CelestialBody;
use App\Repository\CelestialBodyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/celestial-bodies", name="api_")
 */
class CelestialBodyController extends AbstractController
{
    /**
     * @Route(name="celestial_bodies_list", methods={"GET"})
     */
    public function getAll(CelestialBodyRepository $celestialBodyRepository)
    {
        $celestialBodies = $celestialBodyRepository->findAll();

        return $this->json([
            $celestialBodies,
            Response::HTTP_OK,
            array()
        ]);
    }

    /**
     * @Route("/{slug}", name="celestial_body", methods={"GET"})
     */
    public function getOne(CelestialBody $celestialBody = null)
    {
        if ($celestialBody === null) {
            return new JsonResponse(
                ['error' => 'celestial body not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json([
            $celestialBody,
            Response::HTTP_OK,
            array(),
            'groups' => 'celestial-body'
        ]);
    }
}