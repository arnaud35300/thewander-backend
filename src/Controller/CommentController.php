<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Service\Censor;
use App\Entity\CelestialBody;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route(name="api_")
 */
class CommentController extends AbstractController
{
    /**
     *? Retrieves all the comments.
     *
     * @param CommentRepository $commentRepository The Comment repository.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_MODERATOR", statusCode=404)
     * 
     ** @Route("/comments", name="comments_list", methods={"GET"})
     */
    public function getAll(CommentRepository $commentRepository): JsonResponse
    {
        $comments = $commentRepository->findAll();

        return $this->json(
            $comments,
            Response::HTTP_OK,
            array(),
            ['groups' => 'comments']
        );
    }

    /**
     *? Retrieves a particular comment.
     * 
     * @param Comment $comment The Comment entity.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_MODERATOR", statusCode=404)
     * 
     ** @Route("/comments/{id}", name="comment", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function getOne(Comment $comment = null): JsonResponse
    {
        if ($comment === null)
            return $this->json(
                ['information' => 'Comment not found.'],
                Response::HTTP_NOT_FOUND
            );

        return $this->json(
            $comment,
            Response::HTTP_OK,
            array(),
            ['groups' => 'comments']
        );
    }

    /**
     *? Adds a new comment on a particular celestial body.
     * 
     * @param Request $request The HttpFoundation Request class.
     * @param CelestialBody $celestialBody The CelestialBody entity.
     * @param SerializerInterface $serializer The Serializer component.
     * @param ValidatorInterface $validator The Validator component.
     * @param Censor $censor The Censor service.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/celestial-bodies/{slug}/comments", name="create_comment", methods={"POST"})
     */
    public function create(
        Request $request,
        CelestialBody $celestialBody = null,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Censor $censor
    ): JsonResponse 
    {
        if ($celestialBody === null)
            return $this->json(
                ['information' => 'Celestial body not found.'],
                Response::HTTP_NOT_FOUND
            );

        $content = $request->getContent();

        if (json_decode($content) === null)
            return $this->json(
                ['information' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );
        
        if ($censor->check($content) === false)
            return $this->json(
                ['information' => 'Bad words are forbidden.'],
                Response::HTTP_UNAUTHORIZED
            );

        $newComment = $serializer->deserialize(
            $content,
            Comment::class,
            'json',
            ['groups' => 'comment-creation']
        );

        $newComment->setCelestialBody($celestialBody);

        $violations = $validator->validate($newComment);

        if ($violations->count() > 0)
            return $this->json(
                $violations,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        
        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->persist($newComment);
        $manager->flush();

        return $this->json(
            [
                'information' => 'Comment sent.',
                'content' => $newComment
            ],
            Response::HTTP_CREATED,
            array(),
            ['groups' => 'comments']
        );
    }

    /**
     *? Updates a comment.
     * 
     * @param Request $request The HttpFoundation Request class.
     * @param Comment $comment The Comment entity.
     * @param ValidatorInterface $validator The Validator component.
     * @param SerializerInterface $serializer The Serializer component.
     * @param Censor $censor The Censor service.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/comments/{id}", name="update_comment", requirements={"id"="\d+"}, methods={"PATCH"})
     */
    public function update(
        Request $request,
        Comment $comment = null,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        Censor $censor
    ): JsonResponse
    {
        if ($comment === null)
            return $this->json(
                ['information' => 'Comment not found.'],
                Response::HTTP_NOT_FOUND
            );

        $this->denyAccessUnlessGranted('COMMENT_UPDATE', $comment);

        $content = $request->getContent();

        if (json_decode($content) === null)
            return $this->json(
                ['information' => 'Invalid data format.'],
                Response::HTTP_UNAUTHORIZED
            );

        if ($censor->check($content) === false)
            return $this->json(
                ['information' => 'Bad words are forbidden.'],
                Response::HTTP_UNAUTHORIZED
            );

        $content = json_decode($content, true);
        
        $body = !empty($content['body']) ? $content['body'] : $comment->getBody();

        $comment->setBody($body);

        $violations = $validator->validate($comment);

        if ($violations->count() > 0)
            return $this->json(
                $violations,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->persist($comment);
        $manager->flush();

        $comment = $serializer->serialize(
            $comment,
            'json',
            ['groups' => 'comments']
        );
        
        return $this->json(
            [
                'information' => 'Comment now updated.',
                'content' => $comment
            ],
            Response::HTTP_OK
        );
    }

    /**
     *? Deletes a user's comment.
     * 
     * @param Comment $comment The Comment entity.
     * 
     * @return JsonResponse
     * 
     ** @IsGranted("ROLE_CONTRIBUTOR", statusCode=401)
     * 
     ** @Route("/comments/{id}", name="delete_comment", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Comment $comment = null): JsonResponse
    {
        if ($comment === null)
            return $this->json(
                ['information' => 'Comment not found.'],
                Response::HTTP_NOT_FOUND
            );

        $this->denyAccessUnlessGranted('COMMENT_DELETE', $comment);

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->remove($comment);
        $manager->flush();

        return $this->json(
            ['information' => 'Comment now deleted.'],
            Response::HTTP_NO_CONTENT
        );
    }
}