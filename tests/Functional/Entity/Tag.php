<?php

namespace AssoConnect\LogBundle\Tests\Functional\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Tag extends AbstractEntity
{
    public function __construct()
    {
        parent::__construct();
        $this->posts = new ArrayCollection();
    }

    /**
     * @ORM\ManyToMany(targetEntity="Post", mappedBy="tags")
     */
    protected ArrayCollection $posts;

    public function addPost(Post $post): void
    {
        $this->posts[] = $post;
    }
}
