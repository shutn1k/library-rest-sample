<?php

namespace App\Controller\Api;

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Model\ApiResponse;
use App\Service\Author\AuthorService;
use App\Service\Author\Factory\AuthorFactoryInterface;
use App\Utilities\Serializer\ResponseSerializer;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Throwable;

#[Route('/author', name: 'author_')]
class AuthorController extends AbstractController
{
    /**
     * @param AuthorService $authorService
     * @param ResponseSerializer $serializer
     */
    public function __construct(
        private readonly AuthorService $authorService,
        private readonly ResponseSerializer $serializer
    ) {
    }

    /**
     * @param AuthorFactoryInterface $authorFactory
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/api/v1/{locale}/author/',
        operationId: 'authorCreate',
        description: 'Create author',
        requestBody: new OA\RequestBody('#/components/requestBodies/AuthorCreate'),
        tags: ['author'],
        parameters: [new OA\Parameter(
            name: 'locale',
            in: 'path',
            required: true,
            schema: new OA\Schema(ref: '#/components/schemas/Locale')
        )]
    )]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\Response(response: 400, description: 'Bad request')]
    #[OA\Response(response: 409, description: 'Entity already exists')]
    #[OA\Response(response: 500, description: 'Server error')]
    #[Route('/', name: 'create', methods: [Request::METHOD_POST])]
    public function create(AuthorFactoryInterface $authorFactory, Request $request): JsonResponse
    {
        try {
            $this->authorService->createAuthor($authorFactory->create($request));
        } catch (BadRequestException $e) {
            return new JsonResponse(
                $this->serializer->serialize(new ApiResponse(4001, 'Bad request', ['errors' => $e->getErrors()])),
                Response::HTTP_BAD_REQUEST,
                json: true
            );
        } catch (AlreadyExistsException $e) {
            return new JsonResponse(
                $this->serializer->serialize(new ApiResponse(4091, $e->getMessage())),
                Response::HTTP_CONFLICT,
                json: true
            );
        } catch (Throwable) {
            return new JsonResponse(
                $this->serializer->serialize(new ApiResponse(5001, 'Server error')),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                json: true
            );
        }

        return new JsonResponse(
            $this->serializer->serialize(new ApiResponse()),
            Response::HTTP_CREATED,
            json: true
        );
    }

    /**
     * @param int $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/api/v1/{locale}/author/{id}',
        operationId: 'authorReadItem',
        description: 'Get author by id',
        tags: ['author'],
        parameters: [
            new OA\Parameter(
                name: 'locale',
                in: 'path',
                required: true,
                schema: new OA\Schema(ref: '#/components/schemas/Locale')
            ),
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ]
    )]
    #[OA\Response(response: 200, description: 'AOK', content: new OA\JsonContent(
        properties: [new OA\Property(
            'data',
            required: ['author'],
            properties: [new OA\Property('author', ref: '#/components/schemas/Author')],
            type: 'object'
        )],
        type: 'object',
        allOf: [new OA\Schema('#/components/schemas/ApiResponse')]
    ))]
    #[OA\Response(response: 404, description: 'Not found')]
    #[OA\Response(response: 500, description: 'Server error')]
    #[Route('/{id<\d+>}', name: 'item', methods: [Request::METHOD_GET])]
    public function item(int $id, Request $request): JsonResponse
    {
        try {
            return new JsonResponse(
                $this->serializer->serialize(
                    new ApiResponse(data: ['author' => $this->authorService->getAuthor($id, $request->getLocale())]),
                    context: [AbstractNormalizer::GROUPS => ['api_v1', 'api_v1_author']]
                ),
                json: true
            );
        } catch (NotFoundException) {
            return new JsonResponse(
                $this->serializer->serialize(new ApiResponse(4041, 'Not found')),
                Response::HTTP_NOT_FOUND,
                json: true
            );
        } catch (Throwable) {
            return new JsonResponse(
                $this->serializer->serialize(new ApiResponse(5001, 'Server error')),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                json: true
            );
        }
    }
}
