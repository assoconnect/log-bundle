<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Functional\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Author extends AbstractEntity
{
    public function __construct()
    {
        parent::__construct();
        $this->registeredAt = new \DateTime();
        $this->posts = new ArrayCollection();
    }

    #[ORM\Column(type: 'email')]
    protected ?string $email = null;

    #[ORM\Column(type: 'date_absolute')]
    protected DateTimeInterface $registeredAt;

    #[ORM\OneToMany('author', Post::class)]
    protected ArrayCollection $posts;

    #[ORM\OneToOne(targetEntity: Address::class)]
    #[ORM\JoinColumn(referencedColumnName: 'id', nullable: true)]
    protected ?Address $address = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getRegisteredAt(): DateTimeInterface
    {
        return $this->registeredAt;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }
}
