<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/properties", name="api_")
 */
class PropertyController extends AbstractController
{
    /**
     *? Retrieves all the properties.
     *
     * @param PropertyRepository $propertyRepository The Property repository.
     * 
     * @return JsonResponse
     *  
     ** @Route(name="properties_list", methods={"GET"})
     */
    public function getAll(PropertyRepository $propertyRepository): JsonResponse
    {
        $properties = $propertyRepository->findAll();

        return $this->json(
            $properties,
            Response::HTTP_OK,
            array(),
            ['groups' => 'properties']
        );
    }
}
