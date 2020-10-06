<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Entity;

use AssoConnect\LogBundle\Tests\Functional\Entity\FunctionalLog;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class LogTest extends TestCase
{
    public function testContructor()
    {
        $entity = new FunctionalLog(
            $this->createMock(UuidInterface::class),
            $entityClass = 'entityClass',
            $entityColumn = 'entityColumn',
            $entityOldValue = 'oldValue',
            $entityId = 'f749d691-a546-424c-842a-1728a4e96250',
            $requestTrace = 'request trace',
            $date = new \Datetime()
        );
        $this->assertSame($entityClass, $entity->getEntityClass());
        $this->assertSame($entityColumn, $entity->getEntityColumn());
        $this->assertSame($entityOldValue, $entity->getEntityOldValue());
        $this->assertSame($entityId, $entity->getEntityId());
        $this->assertSame($requestTrace, $entity->getRequestTrace());
        $this->assertSame($date, $entity->getCreatedAt());
    }

    /**
     * @dataProvider createBaseDoctrineLog
     */
    public function testRequestMethod(FunctionalLog $functionalLog)
    {
        $requestMethod = 'request method';

        $functionalLog->setRequestMethod($requestMethod);
        $this->assertSame($requestMethod, $functionalLog->getRequestMethod());
    }

    /**
     * @dataProvider createBaseDoctrineLog
     */
    public function testRequestUrl(FunctionalLog $functionalLog)
    {
        $requestUrl = 'https://www.doctrine.org';

        $functionalLog->setRequestUrl($requestUrl);
        $this->assertSame($requestUrl, $functionalLog->getRequestUrl());
    }

    /**
     * @dataProvider createBaseDoctrineLog
     */
    public function testRequestIp(FunctionalLog $functionalLog)
    {
        $requestIp = '127.0.0.1';

        $functionalLog->setRequestIp($requestIp);
        $this->assertSame($requestIp, $functionalLog->getRequestIp());
    }

    private function createBaseDoctrineLog(): FunctionalLog
    {
        return new FunctionalLog(
            $this->createMock(UuidInterface::class),
            'entityClass',
            'entityColumn',
            'oldValue',
            'f749d691-a546-424c-842a-1728a4e96250',
            'request trace',
            new \Datetime()
        );
    }
}
