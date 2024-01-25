<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Functional\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Tag extends AbstractEntity
{
    public function __construct()
    {
        parent::__construct();
        $this->posts = new ArrayCollection();
    }

    #[ORM\ManyToMany(Post::class, 'tags')]
    protected ArrayCollection $posts;

    public function addPost(Post $post): void
    {
        $this->posts[] = $post;
    }
}
