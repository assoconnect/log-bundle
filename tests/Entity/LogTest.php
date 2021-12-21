<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Entity;

use AssoConnect\LogBundle\Tests\Functional\Entity\FunctionalLog;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class LogTest extends TestCase
{
    public function testContructor()
    {
        $entity = new FunctionalLog(
            Uuid::uuid1(),
            $entityClass = 'entityClass',
            $entityColumn = 'entityColumn',
            $entityOldValue = 'oldValue',
            $entityId = 'f749d691-a546-424c-842a-1728a4e96250',
            $requestTrace = 'request trace',
            $date = new \DateTime()
        );
        self::assertNotEmpty($entity->getId());
        self::assertSame($entityClass, $entity->getEntityClass());
        self::assertSame($entityColumn, $entity->getEntityColumn());
        self::assertSame($entityOldValue, $entity->getEntityOldValue());
        self::assertSame($entityId, $entity->getEntityId());
        self::assertSame($requestTrace, $entity->getRequestTrace());
        self::assertSame($date, $entity->getCreatedAt());
    }

    /**
     * @dataProvider createBaseDoctrineLogDataProvider
     */
    public function testRequestMethod(FunctionalLog $functionalLog)
    {
        $requestMethod = 'request method';

        $functionalLog->setRequestMethod($requestMethod);
        self::assertSame($requestMethod, $functionalLog->getRequestMethod());
    }

    /**
     * @dataProvider createBaseDoctrineLogDataProvider
     */
    public function testRequestUrl(FunctionalLog $functionalLog)
    {
        $requestUrl = 'https://www.doctrine.org';

        $functionalLog->setRequestUrl($requestUrl);
        self::assertSame($requestUrl, $functionalLog->getRequestUrl());
    }

    /**
     * @dataProvider createBaseDoctrineLogDataProvider
     */
    public function testRequestIp(FunctionalLog $functionalLog)
    {
        $requestIp = '127.0.0.1';

        $functionalLog->setRequestIp($requestIp);
        self::assertSame($requestIp, $functionalLog->getRequestIp());
    }

    public function createBaseDoctrineLogDataProvider(): \Iterator
    {
        yield 'basic Log' => [
            new FunctionalLog(
                $this->createMock(UuidInterface::class),
                'entityClass',
                'entityColumn',
                'oldValue',
                'f749d691-a546-424c-842a-1728a4e96250',
                'request trace',
                new \DateTime()
            )
        ];
    }
}
