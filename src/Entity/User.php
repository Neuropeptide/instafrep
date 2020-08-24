<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullName;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="author", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\ManyToMany(targetEntity=Post::class, inversedBy="likers")
     */
    private $likedPosts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uploadImage;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="author", orphanRemoval=true)
     */
    private $notifications;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="following")
     */
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="followers")
     */
    private $following;

    /**

    /**
     * @ORM\OneToMany(targetEntity=Group::class, mappedBy="creator", orphanRemoval=true)
     */
    private $groupCreator;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, mappedBy="members")
     */
    private $groupMember;


    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->posts = new ArrayCollection();
        $this->likedPosts = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->groupCreator = new ArrayCollection();
        $this->groupMember = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        // On ne met le slug de manière automatique que lors de la création de l'utilisateur
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $slug = $slugify->slugify($username);
            $this->setSlug($slug);
        }

        $this->username = $username;


        return $this;
    }

    public function getMiniature() {
            return str_replace('.png', '-mini.png',$this->uploadImage);
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->created_at = $createdAt;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getLikedPosts(): Collection
    {
        return $this->likedPosts;
    }

    public function like(Post $likedPost): self
    {
        if (!$this->likedPosts->contains($likedPost)) {
            $this->likedPosts[] = $likedPost;
        }
        return $this;
    }

    public function unlike(Post $likedPost): self
    {
        if ($this->likedPosts->contains($likedPost)) {
            $this->likedPosts->removeElement($likedPost);
        }

        return $this;
    }

    /**
     * Does this User currently likes the given Post ?
     * @param Post $post
     * @return bool
     */
    public function doesLike(Post $post) {
        return $this->likedPosts->contains($post);
    }

    public function getUploadImage(): ?string
    {
        return $this->uploadImage;
    }

    public function setUploadImage(?string $uploadImage): self
    {
        $this->uploadImage = $uploadImage;
        return $this;
    }
    
    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setAuthor($this);
        }
    }
    
    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getAuthor() === $this) {
                $notification->setAuthor(null);
            }
        }
    }

    /**
     * @return Collection|self[]
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(self $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers[] = $follower;
        }
        return $this;
    }

    public function removeFollower(self $follower): self
    {
        if ($this->followers->contains($follower)) {
            $this->followers->removeElement($follower);
        }
        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function addFollowing(self $following): self
    {
        if (!$this->following->contains($following)) {

            $this->following[] = $following;
            $following->addFollower($this);
        }

        return $this;
    }

    public function removeFollowing(self $following): self
    {
        if ($this->following->contains($following)) {
            $this->following->removeElement($following);
            $following->removeFollower($this);
        }
        return $this;
    }

    /**
     * Check if the verified role is correctly set
     * @return bool
     */
    public function isVerified(): bool
    {
        return in_array('ROLE_VERIFIED', $this->getRoles());
    }

    /**
     * get group created by the user
     * @return Collection|Group[]
     */
    public function getGroupCreator(): Collection
    {
        return $this->groupCreator;
    }

    public function addGroupCreator(Group $groupCreator): self
    {
        if (!$this->groupCreator->contains($groupCreator)) {
            $this->groupCreator[] = $groupCreator;
            $groupCreator->setCreator($this);
        }

        return $this;
    }

    public function removeGroupCreator(Group $groupCreator): self
    {
        if ($this->groupCreator->contains($groupCreator)) {
            $this->groupCreator->removeElement($groupCreator);
            // set the owning side to null (unless already changed)
            if ($groupCreator->getCreator() === $this) {
                $groupCreator->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * get group followed by the user
     * @return Collection|Group[]
     */
    public function getGroupMember(): Collection
    {
        return $this->groupMember;
    }

    public function addGroupMember(Group $groupMember): self
    {
        if (!$this->groupMember->contains($groupMember)) {
            $this->groupMember[] = $groupMember;
            $groupMember->addMember($this);
        }

        return $this;
    }

    public function removeGroupMember(Group $groupMember): self
    {
        if ($this->groupMember->contains($groupMember)) {
            $this->groupMember->removeElement($groupMember);
            $groupMember->removeMember($this);
        }

        return $this;
    }

    /**
     * @param Group $group
     * @return bool
     */
    public function isMemberOf($group){
        return $this->getGroupMember()->contains($group);

    }

    /**
     * @param User $user
     * @return bool
     */
    public function doesFollow($user){
        return $this->getFollowing()->contains($user);
    }
}

