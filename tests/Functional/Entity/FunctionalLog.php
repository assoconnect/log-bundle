<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Functional\Entity;

use AssoConnect\LogBundle\Entity\Log;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class FunctionalLog extends Log
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid_binary_ordered_time', unique: true)]
    protected UuidInterface $id;

    public function __construct(
        object $entity,
        string $entityColumn,
        ?string $entityOldValue,
        string $requestTrace,
    ) {
        $this->id = Uuid::uuid1();
        $this->entityId = (string) $entity->getId();
        parent::__construct(
            $entity,
            $entityColumn,
            $entityOldValue,
            $requestTrace,
        );
    }

    #[ORM\Column(type: 'uuid_binary_ordered_time')]
    protected string $entityId;

    public function getEntityId(): string
    {
        return $this->entityId;
    }
}
