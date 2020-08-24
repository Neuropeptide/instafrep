<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @ORM\Table(name="`group`")
 */
class Group
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="groupCreator")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="groupMember")
     */
    private $members;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="papaGroup")
     */
    private $postInGroup;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->postInGroup = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPostInGroup(): Collection
    {
        return $this->postInGroup;
    }

    public function addPostInGroup(Post $postInGroup): self
    {
        if (!$this->postInGroup->contains($postInGroup)) {
            $this->postInGroup[] = $postInGroup;
            $postInGroup->setPapaGroup($this);
        }

        return $this;
    }

    public function removePostInGroup(Post $postInGroup): self
    {
        if ($this->postInGroup->contains($postInGroup)) {
            $this->postInGroup->removeElement($postInGroup);
            // set the owning side to null (unless already changed)
            if ($postInGroup->getPapaGroup() === $this) {
                $postInGroup->setPapaGroup(null);
            }
        }

        return $this;
    }


}


