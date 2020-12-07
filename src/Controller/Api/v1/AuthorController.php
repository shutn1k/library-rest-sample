<?php

namespace App\Controller\Api\v1;

use App\Manager\AuthorManager;
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
 * Class AuthorController
 * @package App\Controller\Api\v1
 * @Route("/author", name="author_")
 */
class AuthorController extends AbstractController {

    /** @var AuthorManager */
    private $authorManager;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * AuthorController constructor.
     *
     * @param AuthorManager $authorManager
     */
    public function __construct(AuthorManager $authorManager) {

        // Можно наследовать от интерфейса
        $this->authorManager = $authorManager;

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $this->serializer = new Serializer([$normalizer], [new JsonEncoder()]);
    }


    /**
     * @Route("/create", name="create", methods={"post"})
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response {

        try {
            $author = $this->authorManager->create($request->getContent());
        } catch (Exception $e) {

            return $this->json(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

        return new JsonResponse($this->serializer->serialize($author, 'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => [
                'translations', 'newTranslations', 'currentLocale', 'defaultLocale', 'books',
            ]]
        ), 200, [], true);
    }
}
