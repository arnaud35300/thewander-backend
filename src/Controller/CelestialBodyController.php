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
     *? Retrieves all the celestial bodies.
     * 
     * @param object $celestialBodyRepository The CelestialBody repository.
     * 
     * @return JsonResponse
     * 
     ** @Route(name="celestial_bodies_list", methods={"GET"})
     */
    public function getAll(CelestialBodyRepository $celestialBodyRepository): JsonResponse
    {
        $celestialBodies = $celestialBodyRepository->findAll();

        return $this->json(
            $celestialBodies,
            Response::HTTP_OK,
            array(),
            ['groups' => 'celestial-bodies']
        );
    }

    /**
     *? Retrieves a particular celestial body.
     * 
     * @param object $celestialBody The CelestialBody entity.
     * 
     * @return JsonResponse
     * 
     ** @Route("/{slug}", name="celestial_body", methods={"GET"})
     */
    public function getOne(CelestialBody $celestialBody = null): JsonResponse
    {
        if ($celestialBody === null) {
            return $this->json(
                ['error' => 'celestial body not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $celestialBody,
            Response::HTTP_OK,
            array(),
            ['groups' => 'celestial-body']
        );
    }

    /**
     *? Creates a new celestial body.
     * 
     * @param object $request The HttpFoundation Request class.
     * @param object $serializer The Serializer component.
     * @param object $validator The Validator component.
     * 
     * @return JsonResponse
     * 
     ** @Route(name="create_celestial_body", methods={"POST"})
     */
    public function create(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        // TODO : authentication requirements

        $content = $request->getContent();

        if (json_decode($content) === null) {
            return $this->json(
                ['error' => 'invalid data format'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $newCelestialBody = $serializer->deserialize(
            $content,
            CelestialBody::class,
            'json',
            ['groups' => 'celestial-body-creation']
        );

        $errors = $validator->validate($newCelestialBody);

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
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->persist($newCelestialBody);
        $manager->flush();

        return $this->json(
            [
                'message' => 'celestial body created',
                'content' => $newCelestialBody
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     *? Updates a particular celestial body.
     * 
     * @param object $celestialBody The CelestialBody entity.
     * @param object $request The HttpFoundation Request class.
     * @param object $serializer The Serializer component.
     * @param object $validator The Validator component.
     * 
     * @return JsonResponse
     * 
     ** @Route("/{slug}", name="update_celestial_body", methods={"PATCH"})
     */
    public function update(
        CelestialBody $celestialBody = null,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {
        // TODO : authentication requirements
        // ! Note : properties ID JSON

        if ($celestialBody === null) {
            return $this->json(
                ['error' => 'this celestial body does not exist'],
                Response::HTTP_NOT_FOUND
            );
        }

        $content = $request->getContent();

        if (json_decode($content) === null) {
            return $this->json(
                ['error' => 'invalid data format'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $updatedCelestialBody = $serializer->deserialize(
            $content,
            CelestialBody::class,
            'json',
            ['groups' => 'celestial-body-update']
        );

        $errors = $validator->validate($updatedCelestialBody);

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
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $manager = $this
            ->getDoctrine()
            ->getManager();

        $manager->persist($updatedCelestialBody);
        $manager->flush();

        return $this->json(
            [
                'message' => 'celestial body updated',
                'content' => $updatedCelestialBody
            ],
            Response::HTTP_OK
        );
    }

    /**
     *? Deletes a particular celestial body.
     * 
     * @param object $celestialBody The CelestialBody entity.
     * 
     * @return JsonResponse
     * 
     ** @Route("/{slug}", name="delete_celestial_body", methods={"DELETE"})
     */
    public function delete(CelestialBody $celestialBody = null): JsonResponse
    {
        // TODO : authentication requirements

        if ($celestialBody === null) {
            return $this->json(
                ['error' => 'this celestial body does not exist'],
                Response::HTTP_NOT_FOUND
            );
        }

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->remove($celestialBody);
        $manager->flush();

        return $this->json(
            ['message' => 'celestial body deleted'],
            Response::HTTP_NO_CONTENT
        );
    }
}