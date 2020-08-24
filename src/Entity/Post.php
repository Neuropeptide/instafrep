<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

use App\Validator\NoBadWords as AssertNoBadWords;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @AssertNoBadWords
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $title;

    /**
     * @Assert\Length(
     *     min=2,
     *     max=140,
     *     minMessage="Développe un peu",
     *     maxMessage="Raconte pas ta vie !"
     * )
     * @AssertNoBadWords
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @Assert\PositiveOrZero()
     * @ORM\Column(type="integer")
     */
    private $views;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $published_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="likedPosts")
     */
    private $likers;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="comments")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="parent")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=PostNotification::class, mappedBy="linkedPost", orphanRemoval=true)
     */
    private $postNotifs;

    /**
     * @ORM\OneToMany(targetEntity=LikeNotification::class, mappedBy="linkedPost", orphanRemoval=true)
     */
    private $likeNotifications;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="postInGroup")
     */
    private $papaGroup;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        // Valeurs pas défaut de l'entité Post
        $this->setViews(0);
        $this->setCreatedAt(new \DateTime());

        $this->setPublishedAt($this->getCreatedAt());
        $this->likers = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->postNotifs = new ArrayCollection();
        $this->likeNotifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->getLikers()->count();
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->published_at;
    }

    public function setPublishedAt(?\DateTimeInterface $published_at): self
    {
        $this->published_at = $published_at;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Getter qui sert a valider automatiquement le nombre de likes
     * @see https://symfony.com/doc/current/validation.html#getters
     *
     * @Assert\IsTrue(message="Le nombre de likes doit être inférieur ou égal au nombre de vues")
     * @return bool
     *
     * TODO uncomment this validator when the view count is real
     */
    public function isNumberOfLikesValid() {
        return $this->getLikes() <= $this->getViews();
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getLikers(): Collection
    {
        return $this->likers;
    }

    public function addLiker(User $liker): self
    {
        if (!$this->likers->contains($liker)) {
            $this->likers[] = $liker;
            $liker->like($this);
        }

        return $this;
    }

    public function removeLiker(User $liker): self
    {
        if ($this->likers->contains($liker)) {
            $this->likers->removeElement($liker);
            $liker->unlike($this);
        }

        return $this;
    }

    public function isLikedBy(User $user) {
        return $this->likers->contains($user);
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(self $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setParent($this);
        }

        return $this;
    }

    public function removeComment(self $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getParent() === $this) {
                $comment->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PostNotification[]
     */
    public function getPostNotifs(): Collection
    {
        return $this->postNotifs;
    }

    public function addPostNotif(PostNotification $postNotif): self
    {
        if (!$this->postNotifs->contains($postNotif)) {
            $this->postNotifs[] = $postNotif;
            $postNotif->setLinkedPost($this);
        }

        return $this;
    }

    public function removePostNotif(PostNotification $postNotif): self
    {
        if ($this->postNotifs->contains($postNotif)) {
            $this->postNotifs->removeElement($postNotif);
            // set the owning side to null (unless already changed)
            if ($postNotif->getLinkedPost() === $this) {
                $postNotif->setLinkedPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LikeNotification[]
     */
    public function getLikeNotifications(): Collection
    {
        return $this->likeNotifications;
    }

    public function addLikeNotification(LikeNotification $likeNotification): self
    {
        if (!$this->likeNotifications->contains($likeNotification)) {
            $this->likeNotifications[] = $likeNotification;
            $likeNotification->setLinkedPost($this);
        }

        return $this;
    }

    public function removeLikeNotification(LikeNotification $likeNotification): self
    {
        if ($this->likeNotifications->contains($likeNotification)) {
            $this->likeNotifications->removeElement($likeNotification);
            // set the owning side to null (unless already changed)
            if ($likeNotification->getLinkedPost() === $this) {
                $likeNotification->setLinkedPost(null);
            }
        }
        return $this;
    }
    
    /**
     * PapaGroup is the referent group of the post IF IT IS EXISTS !!!!!
     * @return Group|null
     */
    public function getPapaGroup(): ?Group
    {
        return $this->papaGroup;
    }

    /**
     * @param Group|null $papaGroup
     * @return $this
     */
    public function setPapaGroup(?Group $papaGroup): self
    {
        $this->papaGroup = $papaGroup;
        return $this;
    }
}
