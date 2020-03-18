<?php

namespace App\Controller;

use App\Repository\IconRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/icons", name="api_")
 */
class IconController extends AbstractController
{
    /**
     *? Retrieves all the icons.
     *
     * @param IconRepository $iconRepository The Icon repository.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=404)
     *  
     ** @Route(name="icons_list", methods={"GET"})
     */
    public function getAll(IconRepository $iconRepository): JsonResponse
    {
        $icons = $iconRepository->findAll();

        return $this->json(
            $icons,
            Response::HTTP_OK,
            array(),
            ['groups' => 'icons']
        );
    }
}