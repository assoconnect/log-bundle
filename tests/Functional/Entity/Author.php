<?php

namespace AssoConnect\LogBundle\Tests\Functional\Entity;

use AssoConnect\PHPDate\AbsoluteDate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Author extends AbstractEntity
{
    public function __construct()
    {
        parent::__construct();
        $this->registeredAt = new AbsoluteDate('2020-01-01');
        $this->posts = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="email")
     */
    protected ?string $email = null;

    /**
     * @ORM\Column(type="date_absolute")
     */
    protected AbsoluteDate $registeredAt;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="author")
     */
    protected ArrayCollection $posts;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getRegisteredAt(): AbsoluteDate
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(AbsoluteDate $registeredAt): self
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }
}
