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

    public function testRequestMethod()
    {
        $requestMethod = 'request method';

        $entity = $this->createBaseDoctrineLog();
        $entity->setRequestMethod($requestMethod);
        $this->assertSame($requestMethod, $entity->getRequestMethod());
    }

    public function testRequestUrl()
    {
        $requestUrl = 'https://www.doctrine.org';

        $entity = $this->createBaseDoctrineLog();
        $entity->setRequestUrl($requestUrl);
        $this->assertSame($requestUrl, $entity->getRequestUrl());
    }

    public function testRequestIp()
    {
        $requestIp = '127.0.0.1';

        $entity = $this->createBaseDoctrineLog();
        $entity->setRequestIp($requestIp);
        $this->assertSame($requestIp, $entity->getRequestIp());
    }
}
