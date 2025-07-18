<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use App\DataPersister\UserDataPersister;
use App\Repository\UserRepository;
use DateTimeImmutable;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\DataPersister\UserPasswordChangeDataPersister;
use App\DataPersister\UserUpdateDataPersister;
use App\State\Provider\MeProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Dto\UserPasswordUpdateDto;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/register',
            denormalizationContext: ['groups' => ['user:write']],
            validationContext: ['groups' => ['Default']],
            security: "is_granted('PUBLIC_ACCESS')",
            processor: UserDataPersister::class
        ),
        new Get(
            uriTemplate: '/me',
            normalizationContext: ['groups' => ['me:read']],
            security: "is_granted('ROLE_USER')",
            provider: MeProvider::class
        ),
        new Get(
            normalizationContext: ['groups' => ['user:read']],
            // security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            security: "is_granted('ROLE_USER')",
            securityMessage: "Vous ne pouvez supprimer que votre propre compte",
        ),
        new Patch(
            denormalizationContext: ['groups' => ['user:patch']],
            security: "is_granted('ROLE_USER')",
            securityMessage: "Vous ne pouvez modifier que vos propres informations",
            processor: UserUpdateDataPersister::class
        ),
        new Post(
            uriTemplate: '/users/updatepassword',
            input: UserPasswordUpdateDto::class,
            denormalizationContext: ['groups' => ['pass:patch']],
            security: "is_granted('ROLE_USER')",
            securityMessage: "Vous ne pouvez modifier que vos propres informations",
            processor: UserPasswordChangeDataPersister::class
        ),
        new Post(
            uriTemplate: '/users/image',
            denormalizationContext: ['groups' => ['user:image']],
            security: "is_granted('ROLE_USER')",
            securityMessage: "Vous ne pouvez uploader une image que pour votre propre compte",
            validationContext: ['groups' => ['Default']],
            deserialize: false,
            processor: UserUpdateDataPersister::class
        ),
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var Collection<int, Group>
     */
    #[Groups(['me:read'])]
    #[ORM\OneToMany(targetEntity: Group::class, mappedBy: 'creator')]
    private Collection $createdGroups;

    public function getCreatedGroups(): Collection
    {
        return $this->createdGroups;
    }

    public function addCreatedGroup(Group $group): static
    {
        if (!$this->createdGroups->contains($group)) {
            $this->createdGroups->add($group);
            $group->setCreator($this);
        }
        return $this;
    }

    public function removeCreatedGroup(Group $group): static
    {
        if ($this->createdGroups->removeElement($group)) {
            if ($group->getCreator() === $this) {
                $group->setCreator(null);
            }
        }
        return $this;
    }
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['groupeRole:read', 'groupRequest:read', 'groupRequest:readAll', 'me:read', 'user:read', 'group:details', 'comment:read'])]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 180)]
    #[Groups(['user:write', 'me:read'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    #[ORM\Column]
    #[Groups(['user:write', 'pass:patch'])]
    private ?string $password = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    #[ORM\Column(length: 255)]
    #[Groups(['user:write', 'me:read', 'user:read', 'user:patch', 'group:details', 'groupeRole:read', 'groupRequest:read', 'groupRequest:readAll', 'comment:read'])]
    private ?string $name = null;

    #[Assert\NotNull]
    #[Assert\LessThan('-18 years')]
    #[ORM\Column]
    #[Groups(['user:write', 'me:read'])]
    private ?\DateTimeImmutable $birthdate = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column]
    #[Groups(['me:read'])]
    private ?int $score = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:write', 'me:read', 'user:read', 'user:image'])]
    private ?string $imageFilename = null;

    #[Groups(['user:write', 'me:read'])]
    public ?UploadedFile $file = null;

    /**
     * @var Collection<int, Dog>
     */
    #[ORM\OneToMany(targetEntity: Dog::class, mappedBy: 'user', orphanRemoval: true)]
    #[Groups(['me:read', 'user:read'])]
    private Collection $dogs;

    /**
     * @var Collection<int, GroupRole>
     */
    #[ORM\OneToMany(targetEntity: GroupRole::class, mappedBy: 'user', orphanRemoval: true)]
    #[Groups(['me:read'])]
    private Collection $groupRoles;

    /**
     * @var Collection<int, GroupRequest>
     */
    #[ORM\OneToMany(targetEntity: GroupRequest::class, mappedBy: 'user', orphanRemoval: true)]
    #[Groups(['me:read'])]
    private Collection $groupRequests;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $reviews;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'reporter', orphanRemoval: true)]
    private Collection $reports;

    /**
     * @var Collection<int, BlockList>
     */
    #[ORM\OneToMany(targetEntity: BlockList::class, mappedBy: 'blocker', orphanRemoval: true)]
    private Collection $blockLists;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['user:write', 'user:patch', 'me:read'])]
    private ?string $description = null;

    #[Groups(['pass:patch'])]
    private ?string $oldPassword = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2)]
    #[ORM\Column(length: 255)]
    #[Groups(['user:write', 'me:read', 'user:patch'])]
    private ?string $city = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $comments;

    public function __construct(DateTimeImmutable $createdAt = new DateTimeImmutable(), DateTimeImmutable $updatedAt = new DateTimeImmutable())
    {
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->dogs = new ArrayCollection();
        $this->groupRoles = new ArrayCollection();
        $this->groupRequests = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->blockLists = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->createdGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }



    public function getBirthdate(): ?\DateTimeImmutable
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeImmutable $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): static
    {
        $this->imageFilename = $imageFilename;
        return $this;
    }

    /**
     * @return Collection<int, Dog>
     */
    public function getDogs(): Collection
    {
        return $this->dogs;
    }

    public function addDog(Dog $dog): static
    {
        if (!$this->dogs->contains($dog)) {
            $this->dogs->add($dog);
            $dog->setUser($this);
        }

        return $this;
    }

    public function removeDog(Dog $dog): static
    {
        if ($this->dogs->removeElement($dog)) {
            // set the owning side to null (unless already changed)
            if ($dog->getUser() === $this) {
                $dog->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupRole>
     */
    public function getGroupRoles(): Collection
    {
        return $this->groupRoles;
    }

    public function addGroupRole(GroupRole $groupRole): static
    {
        if (!$this->groupRoles->contains($groupRole)) {
            $this->groupRoles->add($groupRole);
            $groupRole->setUser($this);
        }

        return $this;
    }

    public function removeGroupRole(GroupRole $groupRole): static
    {
        if ($this->groupRoles->removeElement($groupRole)) {
            // set the owning side to null (unless already changed)
            if ($groupRole->getUser() === $this) {
                $groupRole->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GroupRequest>
     */
    public function getGroupRequests(): Collection
    {
        return $this->groupRequests;
    }

    public function addGroupRequest(GroupRequest $groupRequest): static
    {
        if (!$this->groupRequests->contains($groupRequest)) {
            $this->groupRequests->add($groupRequest);
            $groupRequest->setUser($this);
        }

        return $this;
    }

    public function removeGroupRequest(GroupRequest $groupRequest): static
    {
        if ($this->groupRequests->removeElement($groupRequest)) {
            // set the owning side to null (unless already changed)
            if ($groupRequest->getUser() === $this) {
                $groupRequest->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setReporter($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getReporter() === $this) {
                $report->setReporter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlockList>
     */
    public function getBlockLists(): Collection
    {
        return $this->blockLists;
    }

    public function addBlockList(BlockList $blockList): static
    {
        if (!$this->blockLists->contains($blockList)) {
            $this->blockLists->add($blockList);
            $blockList->setBlocker($this);
        }

        return $this;
    }

    public function removeBlockList(BlockList $blockList): static
    {
        if ($this->blockLists->removeElement($blockList)) {
            // set the owning side to null (unless already changed)
            if ($blockList->getBlocker() === $this) {
                $blockList->setBlocker(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): static
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }
}
