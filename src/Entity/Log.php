<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass
 */
abstract class Log
{
    public function __construct(
        UuidInterface $id,
        string $entityClass,
        string $entityColumn,
        ?string $entityOldValue,
        string $requestTrace,
        \DateTime $createdAt = null
    ) {
        $this->id = $id;
        $this->entityClass = $entityClass;
        $this->entityColumn = $entityColumn;
        $this->entityOldValue = $entityOldValue;
        $this->requestTrace = $requestTrace;
        $this->createdAt = $createdAt ?? new \DateTime();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary_ordered_time", unique=true)
     */
    protected UuidInterface $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    protected string $entityClass = '';

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    protected string $entityColumn = '';

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    protected ?string $entityOldValue = null;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text", length=65535)
     */
    protected string $requestTrace = '';

    /**
     * @ORM\Column(type="string")
     */
    protected string $requestMethod = '';

    /**
     * @ORM\Column(type="text", length=65535)
     * @Assert\Url()
     */
    protected string $requestUrl = '';


    /**
     * @ORM\Column(type="ip")
     */
    protected string $requestIp = '';

    /**
     * @ORM\Column(type="datetime")
     */
    protected \DateTime $createdAt;

    public function getId(): string
    {
        return $this->id->__toString();
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getEntityColumn(): string
    {
        return $this->entityColumn;
    }

    public function getEntityOldValue(): ?string
    {
        return $this->entityOldValue;
    }

    abstract public function getEntityId(): string;

    public function getRequestTrace(): string
    {
        return $this->requestTrace;
    }

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }


    public function getRequestIp(): string
    {
        return $this->requestIp;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setRequestMethod(string $requestMethod): self
    {
        $this->requestMethod = $requestMethod;

        return $this;
    }

    public function setRequestUrl(string $requestUrl): self
    {
        $this->requestUrl = $requestUrl;

        return $this;
    }

    public function setRequestIp(string $requestIp): self
    {
        $this->requestIp = $requestIp;

        return $this;
    }
}
