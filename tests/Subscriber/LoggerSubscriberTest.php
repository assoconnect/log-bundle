<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Subscriber;

use AssoConnect\LogBundle\Entity\Log;
use AssoConnect\LogBundle\Factory\LogDataFactory;
use AssoConnect\LogBundle\Subscriber\LoggerSubscriber;
use AssoConnect\LogBundle\Tests\Functional\Entity\Address;
use AssoConnect\LogBundle\Tests\Functional\Service\LogFactory;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\UnitOfWork;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoggerSubscriberTest extends KernelTestCase
{
    public function testEventSubscriptionAsAnAttribute(): void
    {
        $reflection = new \ReflectionClass(LoggerSubscriber::class);
        $attributes = $reflection->getAttributes(AsDoctrineListener::class);

        self::assertCount(1, $attributes, 'LoggerSubscriber should have AsDoctrineListener attribute');

        $attribute = $attributes[0]->newInstance();
        self::assertSame(Events::onFlush, $attribute->event);
    }

    public function testSubscriberPersistsLogs(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $unitOfWork = $this->createMock(UnitOfWork::class);
        $cmf = $this->createMock(ClassMetadataFactory::class);
        $em->method('getUnitOfWork')->willReturn($unitOfWork);
        $em->method('getMetadataFactory')->willReturn($cmf);

        $event = new OnFlushEventArgs($em);

        $logDataFactory = $this->createMock(LogDataFactory::class);
        $logDataFactory
            ->expects(self::once())
            ->method('createFromEvent')
            ->with($event)
            ->willReturn([
                ['entity' => new Address(), 'entityColumn' => 'action.create', 'entityOldValue' => ''],
            ]);

        $logFactory = $this->createMock(LogFactory::class);
        $logFactory
            ->expects(self::once())
            ->method('createLogFromEntity')
            ->willReturn($log = $this->createMock(Log::class));

        $em->expects(self::once())->method('persist')->with($log);
        $cmf
            ->expects(self::once())
            ->method('getMetadataFor')
            ->willReturn($metadata = $this->createMock(ClassMetadata::class));
        $unitOfWork->expects(self::once())->method('computeChangeSet')->with($metadata, $log);

        $subscriber = new LoggerSubscriber(
            $logFactory,
            $logDataFactory,
            realpath(__DIR__ .  '/../..')
        );

        $subscriber->onFlush($event);
    }
}
