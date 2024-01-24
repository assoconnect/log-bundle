<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Entity;

use AssoConnect\LogBundle\Tests\Functional\Entity\Address;
use AssoConnect\LogBundle\Tests\Functional\Entity\FunctionalLog;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\User\UserInterface;

class LogTest extends TestCase
{
    public function testContructorAndMethods(): void
    {
        $entity = new FunctionalLog(
            $address = new Address(),
            $entityColumn = 'entityColumn',
            $entityOldValue = 'oldValue',
            $requestTrace = 'request trace',
        );
        self::assertSame(Address::class, $entity->getEntityClass());
        self::assertSame($entityColumn, $entity->getEntityColumn());
        self::assertSame($entityOldValue, $entity->getEntityOldValue());
        self::assertSame((string) $address->getId(), $entity->getEntityId());
        self::assertSame($requestTrace, $entity->getRequestTrace());

        $entity->setRequestContext(
            new RequestContext(
                method: $method = 'GET',
                scheme: 'https',
                host: 'mydomain.com',
                path: '/hello',
                queryString: 'foo=bar'
            ),
            $ip = '127.0.0.1',
        );
        self::assertSame($method, $entity->getRequestMethod());
        self::assertSame('https://mydomain.com/hello?foo=bar', $entity->getRequestUrl());
        self::assertSame($ip, $entity->getRequestIp());

        $entity->setSecurityUser($user = $this->createMock(UserInterface::class));
        self::assertSame($user, $entity->getUser());
    }
}
