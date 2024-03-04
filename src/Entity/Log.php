<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class Log
{
    public const MAX_STRING_LENGTH = 60_000;

    public function __construct(
        object $entity,
        string $entityColumn,
        ?string $entityOldValue,
        string $requestTrace,
    ) {
        $this->entityClass = $entity::class;
        $this->entityColumn = $entityColumn;
        $this->entityOldValue = mb_substr($entityOldValue, 0, Log::MAX_STRING_LENGTH);
        $this->requestTrace = $requestTrace;
        $this->createdAt = new DateTimeImmutable();
    }

    public function setRequestContext(RequestContext $requestContext, string $ip): static
    {
        $this->requestUrl = $requestContext->getScheme() . '://' .
            $requestContext->getHost() .
            $requestContext->getPathInfo() .
            ('' === $requestContext->getQueryString() ? '' : '?' . $requestContext->getQueryString())
        ;
        $this->requestMethod = $requestContext->getMethod();
        $this->requestIp = $ip;

        return $this;
    }

    public function setSecurityUser(UserInterface $user): static
    {
        $this->user = $user;
        return $this;
    }

    #[Assert\NotBlank]
    #[ORM\Column]
    protected string $entityClass = '';

    #[Assert\NotBlank]
    #[ORM\Column]
    protected string $entityColumn = '';

    #[ORM\Column(type: 'text', length: self::MAX_STRING_LENGTH, nullable: true)]
    protected ?string $entityOldValue = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'text', length: self::MAX_STRING_LENGTH)]
    protected string $requestTrace = '';

    #[ORM\Column]
    protected string $requestMethod = '';

    #[Assert\Url]
    #[ORM\Column(type: 'text', length: self::MAX_STRING_LENGTH)]
    protected string $requestUrl = '';

    #[ORM\Column(type: 'ip')]
    protected string $requestIp = '';

    /**
     * This field has to be defined by the implementation to match their security user
     */
    protected ?UserInterface $user = null;

    #[ORM\Column(type: 'datetime_immutable')]
    protected DateTimeImmutable $createdAt;

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

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
