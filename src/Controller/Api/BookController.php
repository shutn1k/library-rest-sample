<?php

namespace App\Controller\Api;

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Form\Search\BookSearchParamsType;
use App\Model\ApiResponse;
use App\Model\Search\BookSearchParams;
use App\Service\Book\BookService;
use App\Service\Book\Factory\BookFactoryInterface;
use App\Utilities\Serializer\ResponseSerializer;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Throwable;

#[Route('/book', name: 'book_')]
class BookController extends AbstractController
{
    /**
     * @param BookService $bookService
     * @param ResponseSerializer $serializer
     */
    public function __construct(
        private readonly BookService $bookService,
        private readonly ResponseSerializer $serializer
    ) {
    }

    /**
     * @param BookFactoryInterface $bookFactory
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/api/v1/{locale}/book/',
        operationId: 'bookCreate',
        description: 'Create book',
        requestBody: new OA\RequestBody(ref: '#/components/requestBodies/BookCreate'),
        tags: ['book'],
        parameters: [
            new OA\Parameter(
                name: 'locale',
                in: 'path',
                required: true,
                schema: new OA\Schema(ref: '#/components/schemas/Locale')
            )
        ]
    )]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\Response(response: 400, description: 'Bad request')]
    #[OA\Response(response: 409, description: 'Entity already exists')]
    #[OA\Response(response: 500, description: 'Server error')]
    #[Route('/', name: 'create', methods: [Request::METHOD_POST])]
    public function create(BookFactoryInterface $bookFactory, Request $request): JsonResponse
    {
        try {
            $this->bookService->createBook($bookFactory->create($request));
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
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/api/v1/{locale}/book/{id}',
        operationId: 'bookReadItem',
        description: 'Get book by id',
        tags: ['book'],
        parameters: [
            new OA\Parameter(
                name: 'locale',
                in: 'path',
                required: true,
                schema: new OA\Schema(ref: '#/components/schemas/Locale')
            ),
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer', minimum: 1))
        ]
    )]
    #[OA\Response(response: 200, description: 'AOK', content: new OA\JsonContent(
        properties: [new OA\Property(
            'data',
            required: ['book'],
            properties: [new OA\Property('book', ref: '#/components/schemas/Book')],
            type: 'object'
        )],
        type: 'object',
        allOf: [new OA\Schema('#/components/schemas/ApiResponse')]
    ))]
    #[OA\Response(response: 404, description: 'Not found')]
    #[OA\Response(response: 500, description: 'Server error')]
    #[Route('/{id<\d+>}', name: 'item', methods: [Request::METHOD_POST])]
    public function item(int $id): JsonResponse
    {
        try {
            return new JsonResponse(
                $this->serializer->serialize(
                    new ApiResponse(data: ['book' => $this->bookService->getBook($id)]),
                    context: [AbstractNormalizer::GROUPS => ['api_v1', 'api_v1_book']]
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/api/v1/{locale}/book/',
        operationId: 'bookList',
        description: 'Search book by params',
        tags: ['book'],
        parameters: [
            new OA\Parameter(
                name: 'locale',
                in: 'path',
                required: true,
                schema: new OA\Schema(ref: '#/components/schemas/Locale')
            ),
            new OA\Parameter(
                name: 'bt',
                description: 'Book title in locale',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', maxLength: 255, minLength: 3)
            )
        ]
    )]
    #[OA\Response(response: 200, description: 'AOK', content: new OA\JsonContent(
        properties: [new OA\Property(
            'data',
            required: ['books'],
            properties: [new OA\Property(
                'books',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/Book')
            )],
            type: 'object'
        )],
        type: 'object',
        allOf: [new OA\Schema('#/components/schemas/ApiResponse')]
    ))]
    #[OA\Response(response: 500, description: 'Server error')]
    #[Route('/', name: 'list', methods: [Request::METHOD_GET])]
    public function list(Request $request): JsonResponse
    {
        $bookSearchParams = new BookSearchParams();
        $bookSearchParamsForm = $this->createForm(BookSearchParamsType::class, $bookSearchParams);
        $bookSearchParamsForm->handleRequest($request);
        $bookSearchParams->setLocale($request->getLocale());

        try {
            return new JsonResponse(
                $this->serializer->serialize(
                    new ApiResponse(
                        data: ['books' => $this->bookService->searchBook($bookSearchParams, $request->getLocale())]
                    ),
                    context: [AbstractNormalizer::GROUPS => ['api_v1', 'api_v1_book']]
                ),
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
