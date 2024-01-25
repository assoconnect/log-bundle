<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Functional\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Address extends AbstractEntity
{
    private ?string $streetName = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function getStreetName(): ?string
    {
        return $this->streetName;
    }
}
