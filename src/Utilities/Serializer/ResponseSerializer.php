<?php

namespace App\Utilities\Serializer;

use App\Entity\Library\Author;
use App\Entity\Library\Book;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseSerializer implements SerializerInterface
{
    private const  DEFAULT_CONTEXT = [
        AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
        AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 2,
    ];

    private SymfonySerializer $serializer;

    public function __construct()
    {
        $normalizers = [
            new DateTimeNormalizer(),
            new ObjectNormalizer(
                new ClassMetadataFactory(new AnnotationLoader()),
                new CamelCaseToSnakeCaseNameConverter(),
                propertyTypeExtractor: new ReflectionExtractor(),
            ),
            new ArrayDenormalizer(),
        ];
        $encoders = [new JsonEncoder()];

        $this->serializer = new SymfonySerializer($normalizers, $encoders);
    }

    /**
     * @inheritDoc
     */
    public function serialize(mixed $data, string $format = 'json', array $context = []): string
    {
        return $this->serializer->serialize(
            $data,
            'json',
            array_merge(self::DEFAULT_CONTEXT, $context)
        );
    }

    /**
     * @inheritDoc
     */
    public function deserialize(mixed $data, string $type, string $format = 'json', array $context = []): mixed
    {
        return $this->serializer->deserialize($data, $type, 'json', $context);
    }
}
