<?php

namespace App\Tests\Manager;

use App\Entity\Author;
use App\Exceptions\JsonDecodeException;
use App\Exceptions\ParserException;
use App\Manager\AuthorManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class BookManagerTest
 * @package App\Tests\Manager
 */
class AuthorManagerTest extends TestCase {

    /** @var EntityManagerInterface|MockObject */
    private $objectManager;

    public function setUp(): void {

        parent::setUp();

        $this->objectManager = $this->createMock(EntityManagerInterface::class);
        $this->objectManager->expects($this->any())
            ->method('persist')
            ->willReturn(true);
        $this->objectManager->expects($this->any())
            ->method('flush')
            ->willReturn(true);
    }

    public function testCreateSuccessful(): void {

        $authorManager = new AuthorManager($this->objectManager);
        $author = $authorManager->create('{"name": "test"}');

        $this->assertInstanceOf(Author::class, $author);
        $this->assertEquals('test', $author->getName());
    }

    public function testCreateBadJson(): void {

        $this->expectException(JsonDecodeException::class);

        $authorManager = new AuthorManager($this->objectManager);
        $authorManager->create('"name": "test"}');
    }

    public function testCreateBadJsonFormat(): void {

        $this->expectException(ParserException::class);

        $authorManager = new AuthorManager($this->objectManager);
        $authorManager->create('{"nome": "test"}');
    }
}
