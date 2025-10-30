<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Factory;

use AssoConnect\LogBundle\Factory\LogDataFactory;
use AssoConnect\LogBundle\Serializer\LogSerializer;
use AssoConnect\LogBundle\Tests\Functional\Entity\Address;
use AssoConnect\LogBundle\Tests\Functional\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LogDataFactoryTest extends KernelTestCase
{
    public function testExcludedEntityIsIgnored(): void
    {
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $em->persist(new Author());
        $em->getUnitOfWork()->computeChangeSets();

        $logDataFactory = new LogDataFactory(new LogSerializer(), [], [Author::class]);

        $logDatas = iterator_to_array($logDataFactory->createFromEvent(new OnFlushEventArgs($em)), false);
        self::assertCount(0, $logDatas);
    }

    public function testNewEntityIsLogged(): void
    {
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $em->persist($createdAuthor = new Author());
        $em->getUnitOfWork()->computeChangeSets();

        $logDataFactory = new LogDataFactory(new LogSerializer(), [], []);

        $logDatas = iterator_to_array($logDataFactory->createFromEvent(new OnFlushEventArgs($em)), false);
        self::assertCount(1, $logDatas);
        self::assertSame($createdAuthor, $logDatas[0]['entity']);
        self::assertSame('action.create', $logDatas[0]['entityColumn']);
    }

    public function testRemovedEntityIsLogged(): void
    {
        list($em, $unitOfWork) = $this->mockEntityManager();

        $unitOfWork->expects(self::once())->method('getScheduledEntityInsertions')->willReturn([]);
        $unitOfWork->expects(self::once())->method('getScheduledEntityUpdates')->willReturn([]);
        $unitOfWork->expects(self::once())->method('getScheduledEntityDeletions')
            ->willReturn([$deletedAuthor = new Author()]);

        $logDataFactory = new LogDataFactory(new LogSerializer(), [], []);

        $logDatas = iterator_to_array($logDataFactory->createFromEvent(new OnFlushEventArgs($em)), false);
        self::assertCount(1, $logDatas);
        self::assertSame($deletedAuthor, $logDatas[0]['entity']);
        self::assertSame('action.delete', $logDatas[0]['entityColumn']);
    }

    public function testUpdatedEntityIsLogged(): void
    {
        list($em, $unitOfWork) = $this->mockEntityManager();

        $unitOfWork->expects(self::once())->method('getScheduledEntityInsertions')->willReturn([]);
        $unitOfWork->expects(self::once())->method('getScheduledEntityUpdates')
            ->willReturn([$updatedAuthor = new Author()]);
        $unitOfWork->expects(self::once())->method('getScheduledEntityDeletions')->willReturn([]);

        $unitOfWork->method('getEntityChangeSet')->with($updatedAuthor)->willReturn(
            [
                'email' => ['test@gmail.com'],
                'registeredAt' => [new \DateTime('2020-10-06')],
                'unmappedField' => ['test'],
                'address' => [new Address()],
            ]
        );

        $logDataFactory = new LogDataFactory(new LogSerializer(), [], []);

        $logDatas = iterator_to_array($logDataFactory->createFromEvent(new OnFlushEventArgs($em)), false);
        self::assertCount(3, $logDatas);
        self::assertSame($updatedAuthor, $logDatas[0]['entity']);
        self::assertSame('email', $logDatas[0]['entityColumn']);
        self::assertSame('"test@gmail.com"', $logDatas[0]['entityOldValue']);
        self::assertSame($updatedAuthor, $logDatas[1]['entity']);
        self::assertSame('registeredAt', $logDatas[1]['entityColumn']);
        self::assertSame($updatedAuthor, $logDatas[2]['entity']);
        self::assertSame('address', $logDatas[2]['entityColumn']);
    }

    /** @return array{0: EntityManagerInterface, 1: UnitOfWork|MockObject} */
    private function mockEntityManager(): array
    {
        $emReal = self::getContainer()->get(EntityManagerInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $unitOfWork = $this->createMock(UnitOfWork::class);
        $em->method('getUnitOfWork')->willReturn($unitOfWork);

        $em->method('getMetadataFactory')->willReturn($emReal->getMetadataFactory());

        return [$em, $unitOfWork];
    }
}
