<?php

namespace App\Entity;

use App\Repository\LikeNotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/** @Entity */
class LikeNotification extends Notification
{

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="likeNotifications")
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
