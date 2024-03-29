<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Functional\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class AbstractEntity
{
    public const LIMIT = 10;

    public function __construct()
    {
        $this->id = rand(0, 10000000);
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function __toString(): string
    {
        return get_called_class() . ' #' . $this->getId();
    }

    /** Unique ID of the entity*/
    #[ORM\Id]
    #[ORM\Column(unique: true)]
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }

    /** Timestamp of the entity's creation */
    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $createdAt;

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /** Timestamp of the entity's last upate */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private DateTimeInterface $updatedAt;

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
