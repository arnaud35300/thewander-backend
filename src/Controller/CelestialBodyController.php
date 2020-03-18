<?php

namespace App\Controller;


use App\Service\Censor;
use App\Entity\Property;
use App\Service\Slugger;
use App\Service\Uploader;
use App\Service\Delimiter;
use App\Entity\CelestialBody;
use App\Repository\IconRepository;
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
     * @param CelestialBody $celestialBody The CelestialBody entity.
     * 
     * @return JsonResponse
     * 
     ** @Route("/{slug}", name="celestial_body", methods={"GET"})
     */
    public function getOne(CelestialBody $celestialBody = null): JsonResponse
    {
        if ($celestialBody === null)
            return $this->json(
                ['message' => 'Celestial body not found.'],
                Response::HTTP_NOT_FOUND
            );

        return $this->json(
            $celestialBody,
            Response::HTTP_OK,
            array(),
            ['groups' => 'celestial-body']
        );
    }

    /**
     *? Checks the defined coordinates' surroundings.
     *
     * @param Request $request The HttpFoundation Request class.
     * @param Delimiter $delimiter The Delimiter service.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/delimiter", name="delimit_celestial_bodies", methods={"POST"})
     */
    public function verifyCoordinates(Request $request, Delimiter $delimiter): JsonResponse
    {
        $content = $request->getContent();
        
        if (json_decode($content) === null)
            return $this->json(
                ['message' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );
        
        $content = json_decode($content, true);

        $xPosition = isset($content['xPosition']) ? $content['xPosition'] : false;
        $yPosition = isset($content['yPosition']) ? $content['yPosition'] : false;

        if ($xPosition === false || $yPosition === false) {
            return $this->json(
                ['message' => 'No coordinate has been defined.'],
                Response::HTTP_UNAUTHORIZED
            );
        } elseif (is_int($xPosition) === false || is_int($yPosition) === false) {
            return $this->json(
                ['message' => 'Coordinates can only be defined by digits.'],
                Response::HTTP_UNAUTHORIZED
            );
        }
        
        if ($delimiter->verifyPositions($xPosition, $yPosition) === false)
            return $this->json(
                ['message' => 'An existing celestial body is already around these coordinates.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        return $this->json(
            ['message' => 'A new celestial body can be created on these coordinates.'],
            Response::HTTP_OK
        );
    }

    /**
     *? Creates a new celestial body.
     * 
     * @param Request $request The HttpFoundation Request class.
     * @param IconRepository $iconRepository The Icon repository.
     * @param SerializerInterface $serializer The Serializer component.
     * @param ValidatorInterface $validator The Validator component.
     * @param Censor $censor The Censor service.
     * @param Slugger $slugger The Slugger service.
     * @param Delimiter $delimiter The Delimiter service.
     * @param Uploader $uploader The Uploader service.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     *
     ** @Route(name="create_celestial_body", methods={"POST"})
     */
    public function create(
        Request $request,
        IconRepository $iconRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Censor $censor,
        Slugger $slugger,
        Delimiter $delimiter,
        Uploader $uploader
    ): JsonResponse
    {
        $content = $request->request->get('json');

        if (json_decode($content) === null)
            return $this->json(
                ['message' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );

        if ($censor->check($content) === false)
            return $this->json(
                ['message' => 'Bad words are forbidden.'],
                Response::HTTP_UNAUTHORIZED
            );

        $newCelestialBody = $serializer->deserialize(
            $content,
            CelestialBody::class,
            'json',
            ['groups' => 'celestial-body-creation']
        );

        $content = json_decode($content, true);

        $properties = !empty($content['properties']) ? $content['properties'] : false;
        $iconID = !empty($content['icon']) ? $content['icon'] : false;

        $violations = $validator->validate($newCelestialBody);

        if ($violations->count() > 0)
            return $this->json(
                $violations,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        $icon = $iconRepository->find($iconID);
    
        if ($icon === null) 
            return $this->json(
                ['message' => 'An icon has to be selected to set a new celestial body.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        $newCelestialBody->setIcon($icon);
        
        $xPosition = $newCelestialBody->getXPosition();
        $yPosition = $newCelestialBody->getYPosition();
        
        $newCelestialBodySlug = $slugger->slugify(
            $newCelestialBody->getName()
        );

        if ($delimiter->verifyPositions($xPosition, $yPosition, $newCelestialBodySlug) === false)
            return $this->json(
                ['message' => 'An existing celestial body is already around these coordinates.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        if ($request->files->get('picture')) {
            $picture = $uploader->upload(
                'pictures',
                $newCelestialBodySlug,
                '_picture',
                'picture'
            );

            if ($picture['status'] === false)
                return $this->json(
                    ['message' => $picture],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );

            $newCelestialBody->setPicture($picture['picture']);
        }

        if ($properties) {
            foreach ($properties as $propertyID) {
                $property = $this
                    ->getDoctrine()
                    ->getRepository(Property::class)
                    ->find($propertyID)
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
     * @param IconRepository $iconRepository The Icon repository.
     * @param CelestialBody $celestialBody The CelestialBody entity.
     * @param SerializerInterface $serializer The Serializer component.
     * @param ValidatorInterface $validator The Validator component.
     * @param Censor $censor The Censor service.
     * @param Delimiter $delimiter The Delimiter service.
     * @param Slugger $slugger The Slugger service.
     * @param Uploader $uploader
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/{slug}", name="update_celestial_body", methods={"POST"})
     */
    public function update(
        Request $request,
        IconRepository $iconRepository,
        CelestialBody $celestialBody = null,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Censor $censor,
        Delimiter $delimiter,
        Slugger $slugger,
        Uploader $uploader
    ): JsonResponse 
    {
        $request->setMethod('PATCH');

        if ($celestialBody === null)
            return $this->json(
                ['message' => 'Celestial body not found.'],
                Response::HTTP_NOT_FOUND
            );
        
        $this->denyAccessUnlessGranted('CELESTIAL_BODY_UPDATE', $celestialBody);

        $content = $request->request->get('json');
 
        if (json_decode($content) === null) {
            return $this->json(
                ['message' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        if ($censor->check($content) === false)
            return $this->json(
                ['message' => 'Bad words are forbidden.'],
                Response::HTTP_UNAUTHORIZED
            );

        $content = json_decode($content, true);

        $iconID = !empty($content['icon']) ? $content['icon'] : false;
        $name = !empty($content['name']) ? $content['name'] : $celestialBody->getName();
        $xPosition = isset($content['xPosition']) ? $content['xPosition'] : $celestialBody->getXPosition();
        $yPosition = isset($content['yPosition']) ? $content['yPosition'] : $celestialBody->getYPosition();
        $description = !empty($content['description']) ? $content['description'] : $celestialBody->getDescription();
        $properties = !empty($content['properties']) ? $content['properties'] : false;

        $previousName = $celestialBody->getName();
        
        $celestialBody
            ->setName($name)
            ->setXPosition($xPosition)
            ->setYPosition($yPosition)
            ->setDescription($description)
        ;
        
        $violations = $validator->validate($celestialBody);

        if ($violations->count() > 0)
            return $this->json(
                $violations,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        $icon = $iconRepository->find($iconID);
    
        if ($icon === null) 
            return $this->json(
                ['message' => 'An icon has to be selected to set a new celestial body.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        $celestialBody->setIcon($icon);

        $xPosition = $celestialBody->getXPosition();
        $yPosition = $celestialBody->getYPosition();

        $celestialBodySlug = $celestialBody->getSlug();

        if ($delimiter->verifyPositions($xPosition, $yPosition, $celestialBodySlug) === false)
            return $this->json(
                ['message' => 'An existing celestial body is already around these coordinates.'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
            
        $celestialBodySlug = $slugger->slugify(
            $celestialBody->getName()
        );
        
        $pictureDirectory = __DIR__ . '/../../public/images/pictures/'; 
        
        if ($request->files->get('picture')) {
            $picture = $uploader->upload(
                'pictures',
                $celestialBodySlug,
                '_picture',
                'picture'
            );

            if ($picture['status'] === false)
                return $this->json(
                    ['message' => $picture],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
        
            if ($celestialBody->getPicture() !== null)
                unlink($pictureDirectory . $celestialBody->getPicture());
            
            $celestialBody->setPicture($picture['picture']);
        }

        if ($celestialBody->getName() !== $previousName) {            
            $pictureName = $celestialBody->getPicture();
            $pattern = '#\b(.*)\_#';
            $replacement = $celestialBodySlug . '_';
            
            $newPictureName = preg_replace(
                $pattern,
                $replacement,
                $pictureName
            );
            
            rename(
                $pictureDirectory . $celestialBody->getPicture(),
                $pictureDirectory . $newPictureName
            );
        
            $celestialBody->setPicture($newPictureName);
        }        
        
        $currentProperties = $celestialBody->getProperties();   

        foreach ($currentProperties as $currentProperty)
            $celestialBody->removeProperty($currentProperty);
            
        if ($properties) {
            foreach ($properties as $propertyID) {
                $property = $this
                    ->getDoctrine()
                    ->getRepository(Property::class)
                    ->find($propertyID)
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
                ['message' => 'Celestial body not found.'],
                Response::HTTP_NOT_FOUND
            );
        
        $this->denyAccessUnlessGranted('CELESTIAL_BODY_DELETE', $celestialBody);

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