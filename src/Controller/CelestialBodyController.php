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

    /**
     * @Route(name="create_celestial_body", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $content = $request->getContent();

        if (json_decode($content) === null) {
            return $this->json(
                ['error' => 'invalid data format'],
                Response::HTTP_UNAUTHORIZED,
                array()
            );
        }

        $celestialBody = $serializer->deserialize(
            $content,
            CelestialBody::class,
            'json',
            ['groups' => 'celestial-body-creation']
        );

        $errors = $validator->validate($celestialBody);

        if (count($errors) !== 0) {
            $errorsList = array();

            foreach ($errors as $error) {
                $errorsList[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }

            return $this->json(
                $errorsList,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                array()
            );
        }

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->persist($celestialBody);
        $manager->flush();

        return $this->json(
            [
                'message' => 'celestial body created',
                'data' => $celestialBody
            ],
            Response::HTTP_OK,
            array()
        );
    }
}