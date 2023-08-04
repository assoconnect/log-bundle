<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Functional\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Post extends AbstractEntity
{
    public function __construct(
        Author $author
    ) {
        $this->author = $author;
        parent::__construct();
        $this->tags = new ArrayCollection();
    }

    /**
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="posts")
     */
    protected Author $author;

    public function getAuthor(): Author
    {
        return $this->author;
    }

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="posts")
     */
    protected ArrayCollection $tags;

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): void
    {
        $tag->addPost($this);
        $this->tags[] = $tag;
    }
}
