<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Functional\Entity;

use AssoConnect\LogBundle\Entity\Log;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

class FunctionalLog extends Log
{
    public function __construct(
        UuidInterface $id,
        string $entityClass,
        string $entityColumn,
        ?string $entityOldValue,
        string $entityId,
        string $requestTrace,
        \DateTime $createdAt = null
    ) {
        $this->entityId = $entityId;

        parent::__construct($id, $entityClass, $entityColumn, $entityOldValue, $requestTrace, $createdAt);
    }

    /**
     * @ORM\Column(type="uuid_binary_ordered_time")
     */
    protected string $entityId;

    /**
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }
}
