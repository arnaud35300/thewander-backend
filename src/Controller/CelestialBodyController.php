<?php

namespace App\Controller;


use App\Service\Slugger;
use App\Entity\Property;
use App\Service\Delimiter;
use App\Entity\CelestialBody;
use App\Repository\CelestialBodyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/celestial-bodies", name="api_")
 */
class CelestialBodyController extends AbstractController
{
    /**
     *? Retrieves all the celestial bodies.
     * 
     * @param CelestialBodyRepository $celestialBodyRepository The CelestialBody repository.
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
     * @param CelestialBodyRepository $celestialBodyRepository The CelestialBody repository.
     * @param CelestialBody $celestialBody The CelestialBody entity.
     * 
     * @return JsonResponse
     * 
     ** @Route("/{slug}", name="celestial_body", methods={"GET"})
     */
    public function getOne(CelestialBody $celestialBody = null): JsonResponse
    {
        if ($celestialBody === null) {
            return $this->json(
                ['error' => 'Celestial body not found.'],
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
     *? Verifies whether the user can create or drag one of his celestial bodies on the precise X and Y positions.
     *
     * @param Request $request The HttpFoundation Request class.
     * @param Delimiter $delimiter The Delimiter service.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/delimit", name="delimit_celestial_bodies", methods={"POST"})
     */
    public function verifyDelimiter(Request $request, Delimiter $delimiter): JsonResponse
    {
        $content = $request->getContent();
        
        if (json_decode($content) === null)
            return $this->json(
                ['error' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );
        
        $content = json_decode($content, true);

        $xPosition = isset($content['xPosition']) ? $content['xPosition'] : false;
        $yPosition = isset($content['yPosition']) ? $content['yPosition'] : false;

        if ($xPosition === false || $yPosition === false) {
            return $this->json(
                ['message' => 'No position has been defined.'],
                Response::HTTP_UNAUTHORIZED
            );
        } elseif (is_int($xPosition) === false || is_int($yPosition) === false) {
            return $this->json(
                ['message' => 'Positions can only be defined by numbers.'],
                Response::HTTP_UNAUTHORIZED
            );
        }
        
        if ($delimiter->verifyPositions($xPosition, $yPosition) === false) {
            return $this->json(
                ['message' => 'Your celestial body is too close to another one.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $this->json(
            ['message' => 'A new celestial body can be created on these coordinates.'],
            Response::HTTP_OK
        );
    }

    /**
     *? Creates a new celestial body.
     * 
     * @param Request $request The HttpFoundation Request class.
     * @param SerializerInterface $serializer The Serializer component.
     * @param ValidatorInterface $validator The Validator component.
     * @param Delimiter $delimiter The Delimiter service.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     *
     ** @Route(name="create_celestial_body", methods={"POST"})
     */
    public function create(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Delimiter $delimiter
    ): JsonResponse
    {
        $content = $request->getContent();

        if (json_decode($content) === null) {
            return $this->json(
                ['error' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $data = json_decode($content);

        $properties = !empty($content['properties']) ? $content['properties'] : false;

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
                    'field'     => $error->getPropertyPath(),
                    'message'   => $error->getMessage()
                ];
            }

            return $this->json(
                $errorsList,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        $xPosition = $newCelestialBody->getXPosition();
        $yPosition = $newCelestialBody->getYPosition();

        if ($delimiter->verifyPositions($xPosition, $yPosition) === false)
            return $this->json(
                ['message' => 'Your celestial body is too close to another one.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        if ($properties) {
            foreach ($properties as $propertyId) {
                $property = $this
                    ->getDoctrine()
                    ->getRepository(Property::class)
                    ->find($propertyId)
                ;
            
                if ($property)
                    $newCelestialBody->addProperty($property);
            }
        }

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->persist($newCelestialBody);
        $manager->flush();

        return $this->json(
            [
                'message' => 'Celestial body now created.',
                'content' => $newCelestialBody
            ],
            Response::HTTP_CREATED,
            array(),
            ['groups' => 'celestial-body']
        );
    }

    /**
     *? Updates a particular celestial body.
     * 
     * @param Request $request The HttpFoundation Request class.
     * @param SerializerInterface $serializer The Serializer component.
     * @param ValidatorInterface $validator The Validator component.
     * @param Delimiter $delimiter The Delimiter service.
     * @param CelestialBody $celestialBody The CelestialBody entity.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/{slug}", name="update_celestial_body", methods={"PATCH"})
     */
    public function update(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Delimiter $delimiter,
        CelestialBody $celestialBody = null
    ): JsonResponse 
    {        
        if ($celestialBody === null)
            return $this->json(
                ['error' => 'Celestial bodies not found.'],
                Response::HTTP_NOT_FOUND
            );
        
        $this->denyAccessUnlessGranted('CELESTIALBODY_UPDATE', $celestialBody);

        $content = $request->getContent();

        if (json_decode($content) === null) {
            return $this->json(
                ['error' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $content = json_decode($content, true);

        $name = !empty($content['name']) ? $content['name'] : $celestialBody->getName();
        $xPosition = isset($content['xPosition']) ? $content['xPosition'] : $celestialBody->getXPosition();
        $yPosition = isset($content['yPosition']) ? $content['yPosition'] : $celestialBody->getYPosition();
        $picture = !empty($content['picture']) ? $content['picture'] : $celestialBody->getPicture();
        $description = !empty($content['description']) ? $content['description'] : $celestialBody->getDescription();
        $properties = !empty($content['properties']) ? $content['properties'] : false;

        $celestialBody->setXPosition($xPosition);
        $celestialBody->setYPosition($yPosition);
        $celestialBody->setPicture($picture);
        $celestialBody->setDescription($description);
        
        $errors = $validator->validate($celestialBody);
        
        if (count($errors) !== 0) {
            $errorsList = array();
            
            foreach ($errors as $error) {
                $errorsList[] = [
                    'field'     => $error->getPropertyPath(),
                    'message'   => $error->getMessage()
                ];
            }
            
            return $this->json(
                $errorsList,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $xPosition = $celestialBody->getXPosition();
        $yPosition = $celestialBody->getYPosition();

        if ($delimiter->verifyPositions($xPosition, $yPosition) === false)
            return $this->json(
                ['message' => 'Your celestial body is too close to another one.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        
        $currentProperties = $celestialBody->getProperties();        
        
        if ($properties) {
            foreach ($currentProperties as $currentProperty)
                $celestialBody->removeProperty($currentProperty);

            foreach ($properties as $propertyId) {
                $property = $this
                    ->getDoctrine()
                    ->getRepository(Property::class)
                    ->find($propertyId)
                ;

                if ($property)
                    $celestialBody->addProperty($property);
            }
        }

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->persist($celestialBody);
        $manager->flush();

        $celestialBody = $serializer->serialize(
            $celestialBody,
            'json',
            ['groups' => 'celestial-body-update']
        );
        
        return $this->json(
            [
                'message' => 'Celestial body now updated.',
                'content' => $celestialBody
            ],
            Response::HTTP_OK
        );
    }

    /**
     *? Deletes a particular celestial body.
     * 
     * @param CelestialBody $celestialBody The CelestialBody entity.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/{slug}", name="delete_celestial_body", methods={"DELETE"})
     */
    public function delete(CelestialBody $celestialBody = null): JsonResponse
    {        
        if ($celestialBody === null)
            return $this->json(
                ['error' => 'This celestial body does not exist.'],
                Response::HTTP_NOT_FOUND
            );
        
        $this->denyAccessUnlessGranted('CELESTIALBODY_DELETE', $celestialBody);

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->remove($celestialBody);
        $manager->flush();

        return $this->json(
            ['message' => 'Celestial body now deleted.'],
            Response::HTTP_NO_CONTENT
        );
    }
}