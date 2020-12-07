<?php

namespace App\Controller\Api\v1;

use App\Converter\RequestToBookFilterConverter;
use App\Manager\BookManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class BookController
 * @package App\Controller\Api\v1
 * @Route("/", name="book_")
 */
class BookController extends AbstractController {

    /** @var BookManager */
    private $bookManager;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * BookController constructor.
     *
     * @param BookManager $bookManager
     */
    public function __construct(BookManager $bookManager) {

        // Можно наследовать от интерфейса
        $this->bookManager = $bookManager;

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $this->serializer = new Serializer([$normalizer], [new JsonEncoder()]);
    }

    /**
     * @Route("/book/create", name="create", methods={"post"})
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response {

        try {
            $book = $this->bookManager->create($request->getContent());
        } catch (Exception $e) {

            return $this->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

        return new JsonResponse($this->serializer->serialize($book, 'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => [
                'translations', 'newTranslations', 'currentLocale', 'defaultLocale',
            ]]
        ), 200, [], true);
    }

    /**
     * @Route("/book/search", name="search", methods={"get"})
     * @param Request $request
     *
     * @return Response
     */
    public function search(Request $request): Response {

        $bookFilter = (new RequestToBookFilterConverter())->convert($request);

        $books = $this->bookManager->index($bookFilter);

        return new JsonResponse($this->serializer->serialize($books, 'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => [
                'translations', 'newTranslations', 'currentLocale', 'defaultLocale'
            ]]
        ), 200, [], true);
    }

    /**
     * @Route("/{_locale}/book/{id}", name="show_localed", methods={"get"})
     * @param int $id
     * @param Request $request
     *
     * @return Response
     */
    public function showLocaled(int $id, Request $request): Response {

        try {
            $book = $this->bookManager->show($id, $request->getLocale());
        } catch (Exception $e) {

            return $this->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

        return new JsonResponse($this->serializer->serialize($book, 'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => [
                'translations', 'newTranslations', 'currentLocale', 'defaultLocale'
            ]]
        ), 200, [], true);
    }
}
