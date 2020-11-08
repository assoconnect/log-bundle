<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Subscriber;

use AssoConnect\LogBundle\Factory\LogFactoryInterface;
use AssoConnect\LogBundle\Serializer\LogSerializer;
use AssoConnect\LogBundle\Subscriber\LoggerSubscriber;
use AssoConnect\LogBundle\Tests\Functional\Entity\Author;
use AssoConnect\LogBundle\Tests\Functional\Entity\Post;
use AssoConnect\LogBundle\Tests\Functional\Entity\Tag;
use AssoConnect\LogBundle\Tests\Functional\Service\LogFactory;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\UnitOfWork;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoggerSubscriberTest extends KernelTestCase
{
    public function testEventSubscription()
    {
        $this->assertSame(
            [Events::onFlush],
            (new LoggerSubscriber(
                $this->createMock(LogSerializer::class),
                $this->createMock(LogFactoryInterface::class),
                [],
                []
            ))->getSubscribedEvents()
        );
    }

    public function testSubscriberCorrectLogsCreation()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $unitOfWork = $this->createMock(UnitOfWork::class);
        $cmf = $this->createMock(ClassMetadataFactory::class);
        $em->method('getUnitOfWork')->willReturn($unitOfWork);
        $em->method('getMetadataFactory')->willReturn($cmf);

        $unitOfWork->expects($this->once())->method('getScheduledEntityInsertions')->willReturn([new Author()]);
        $unitOfWork->expects($this->once())->method('getScheduledEntityUpdates')->willReturn(
            [$updatedAuthor = new Author(), new Post(new Author())]
        );
        $unitOfWork->expects($this->once())->method('getScheduledEntityDeletions')
            ->willReturn([new Tag(), new Author()]);
        $unitOfWork->method('getEntityChangeSet')->with($updatedAuthor)->willReturn(
            [
                'email'         => ['test@gmail.com'],
                'registeredAt'  => [new \DateTime('2020-10-06')],
                'unmappedField' => ['test']
            ]
        );
        $em->method('getClassMetadata')->with(get_class($updatedAuthor))->willReturn(
            $classMetaData = $this->createMock(ClassMetadata::class)
        );
        $classMetaData->expects($this->once())->method('getFieldNames')->willReturn(['email', 'registeredAt']);

        $em->expects($this->exactly(4))->method('persist');
        $cmf->expects($this->exactly(4))->method('getMetadataFor')
            ->willReturn($this->createMock(ClassMetadata::class));
        $unitOfWork->expects($this->exactly(4))->method('computeChangeSet');

        $subscriber = new LoggerSubscriber(
            $this->createMock(LogSerializer::class),
            new LogFactory(),
            ['AssoConnect\LogBundle\Tests\Functional\Entity\Author'],
            ['AssoConnect\LogBundle\Tests\Functional\Entity\Post']
        );

        $event = new OnFlushEventArgs($em);

        $dispatcher = new EventManager();
        $dispatcher->addEventSubscriber($subscriber);
        $dispatcher->dispatchEvent(Events::onFlush, $event);
    }
}
