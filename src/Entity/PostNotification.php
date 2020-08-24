<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;



/** @Entity */
class PostNotification extends Notification
{


    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="postNotifs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $linkedPost;


    public function getLinkedPost(): ?Post
    {
        return $this->linkedPost;
    }

    public function setLinkedPost(?Post $linkedPost): self
    {
        $this->linkedPost = $linkedPost;

        return $this;
    }

}
