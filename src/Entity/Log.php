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
        string $entityId,
        string $requestTrace,
        \DateTime $createdAt = null
    ) {
        $this->id = $id;
        $this->entityClass = $entityClass;
        $this->entityColumn = $entityColumn;
        $this->entityOldValue = $entityOldValue;
        $this->entityId = $entityId;
        $this->requestTrace = $requestTrace;
        $this->createdAt = $createdAt ?? new \DateTime();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid_binary_ordered_time", unique=true)
     */
    protected UuidInterface $id;

    public function getId(): string
    {
        return $this->id->__toString();
    }

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    protected string $entityClass = '';

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    protected string $entityColumn = '';

    public function getEntityColumn(): string
    {
        return $this->entityColumn;
    }

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    protected ?string $entityOldValue = '';

    public function getEntityOldValue(): ?string
    {
        return $this->entityOldValue;
    }

    /**
     * @ORM\Column(type="uuid_binary_ordered_time")
     */
    protected string $entityId;

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text", length=65535)
     */
    protected string $requestTrace = '';

    public function getRequestTrace(): string
    {
        return $this->requestTrace;
    }

    /**
     * @ORM\Column(type="string")
     */
    protected string $requestMethod = '';

    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * @ORM\Column(type="text", length=65535)
     * @Assert\Url()
     */
    protected string $requestUrl = '';

    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    /**
     * @ORM\Column(type="ip")
     */
    protected string $requestIp = '';

    public function getRequestIp(): string
    {
        return $this->requestIp;
    }

    /**
     * @ORM\Column(type="datetime")
     */
    protected \DateTime $createdAt;

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param string $requestMethod
     */
    public function setRequestMethod(string $requestMethod): self
    {
        $this->requestMethod = $requestMethod;

        return $this;
    }

    /**
     * @param string $requestUrl
     */
    public function setRequestUrl(string $requestUrl): self
    {
        $this->requestUrl = $requestUrl;

        return $this;
    }

    /**
     * @param string $requestIp
     */
    public function setRequestIp(string $requestIp): self
    {
        $this->requestIp = $requestIp;

        return $this;
    }
}
