<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/comments", name="api_")
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
     ** @Route(name="comments_list", methods={"GET"})
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
     ** @Route("/{id}", name="comment", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function getOne(Comment $comment = null): JsonResponse
    {
        if ($comment === null) {
            return $this->json(
                ['error' => 'comment not found'],
                Response::HTTP_NOT_FOUND
            );
        }

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
     * @param SerializerInterface $serializer The Serializer component.
     * @param ValidatorInterface $validator
     * 
     * @return JsonResponse
     * 
     ** @Route(name="create_comment", methods={"POST"})
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

        $newComment = $serializer->deserialize(
            $content,
            Comment::class,
            'json',
            ['groups' => 'comment-creation']
        );

        $errors = $validator->validate($newComment);

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

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->persist($newComment);
        $manager->flush();

        return $this->json(
            [
                'message' => 'comment sent',
                'content' => $newComment
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     *? Updates a user's comment.
     * 
     * @param Request $request The HttpFoundation Request class.
     * @param SerializerInterface $serializer The Serializer component.
     * @param ValidatorInterface $validator The Validator component.
     * @param User $user The user entity.
     * 
     * @return JsonResponse
     * 
     ** @Route("/{id}", name="update_comment", requirements={"id"="\d+"}, methods={"PATCH"})
     */
    public function update(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Comment $comment = null
    ): JsonResponse {
        // TODO : authentication requirements

        if ($comment === null) {
            return $this->json(
                ['error' => 'comment not found.'],
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

        $content = json_decode($content, true);
        
        $body = !empty($content['body']) ? $content['body'] : $comment->getBody();

        $comment->setBody($body);

        $errors = $validator->validate($comment);

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

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->persist($comment);
        $manager->flush();

        $comment = $serializer->serialize(
            $comment,
            'json',
            ['groups' => 'comment-update']
        );
        
        return $this->json(
            [
                'message' => 'comment updated',
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
     ** @Route("/{id}", name="delete_comment", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Comment $comment =  null): JsonResponse
    {
        // TODO : authentication requirements

        if ($comment === null) {
            return $this->json(
                ['error' => 'the comment does not exist.'],
                Response::HTTP_NOT_FOUND
            );
        }

        $manager = $this
            ->getDoctrine()
            ->getManager()
        ;

        $manager->remove($comment);
        $manager->flush();

        return $this->json(
            ['message' => 'comment deleted'],
            Response::HTTP_NO_CONTENT
        );
    }
}
