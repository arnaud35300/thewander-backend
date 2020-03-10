<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=404)
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
