<?php

namespace App\Entity;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\DataPersister\GroupDataPersister;
use App\Repository\GroupRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Operation;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['group:details']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['group:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['group:write']],
            security: "is_granted('ROLE_USER')",
            processor: GroupDataPersister::class,
            securityMessage: "Seuls les utilisateurs connectés peuvent créer des groupes"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['group:write']],
            security: "is_granted('GROUP_EDIT', object)",
            securityMessage: "Vous ne pouvez modifier que vos propres groupes"
        ),
        new Delete(
            security: "is_granted('GROUP_DELETE', object)",
            securityMessage: "Vous ne pouvez supprimer que vos propres groupes"
        ),
        new Get(
            name: 'get_group_with_reviews',
            uriTemplate: '/groups-reviews',
            normalizationContext: ['groups' => ['group:reviews_only']],
            security: "is_granted('ROLE_USER')",
            securityMessage: "Vous n'avez pas les droits pour accéder à cette ressource"
        ),
    ]
)]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['group:read', 'groupeRole:read', 'groupRequest:read', 'groupRequest:readAll', 'group:reviews_only', 'group:details'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['group:read', 'group:details', 'group:write'])]
    private ?bool $mixed = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['group:read', 'group:write', 'group:details'])]
    private ?string $comment = null;

    #[ORM\Column]
    #[Groups(['group:details'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['group:details'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['group:read', 'group:write', 'group:details', 'groupeRole:read', 'groupRequest:read', 'groupRequest:readAll', 'group:reviews_only', 'me:read'])]
    private ?string $name = null;

    /**
     * @var Collection<int, GroupRole>
     */
    #[ORM\OneToMany(targetEntity: GroupRole::class, mappedBy: 'walkGroup', orphanRemoval: true)]
    #[Groups(['group:details', 'me:read'])]
    private Collection $groupRoles;

    /**
     * @var Collection<int, GroupRequest>
     */
    #[ORM\OneToMany(targetEntity: GroupRequest::class, mappedBy: 'walkGroup', orphanRemoval: true)]
    private Collection $groupRequests;

    /**
     * @var Collection<int, Walk>
     */
    #[ORM\OneToMany(targetEntity: Walk::class, mappedBy: 'walkGroup', orphanRemoval: true)]
    #[Groups(['group:details', 'me:read'])]
    private Collection $walks;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'walkGroup', orphanRemoval: true)]
    #[Groups(['group:details', 'me:read'])]
    private Collection $reviews;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'group_id', orphanRemoval: true)]
    private Collection $comments;


    public function __construct(DateTimeImmutable $createdAt = new DateTimeImmutable(), DateTimeImmutable $updatedAt = new DateTimeImmutable())
    {
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->groupRoles = new ArrayCollection();
        $this->groupRequests = new ArrayCollection();
        $this->walks = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isMixed(): ?bool
    {
        return $this->mixed;
    }

    public function setMixed(bool $mixed): static
    {
        $this->mixed = $mixed;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

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

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
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
            $groupRole->setWalkGroup($this);
        }

        return $this;
    }

    public function removeGroupRole(GroupRole $groupRole): static
    {
        if ($this->groupRoles->removeElement($groupRole)) {
            // set the owning side to null (unless already changed)
            if ($groupRole->getWalkGroup() === $this) {
                $groupRole->setWalkGroup(null);
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
            $groupRequest->setWalkGroup($this);
        }

        return $this;
    }

    public function removeGroupRequest(GroupRequest $groupRequest): static
    {
        if ($this->groupRequests->removeElement($groupRequest)) {
            // set the owning side to null (unless already changed)
            if ($groupRequest->getWalkGroup() === $this) {
                $groupRequest->setWalkGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Walk>
     */
    public function getWalks(): Collection
    {
        return $this->walks;
    }

    public function addWalk(Walk $walk): static
    {
        if (!$this->walks->contains($walk)) {
            $this->walks->add($walk);
            $walk->setWalkGroup($this);
        }

        return $this;
    }

    public function removeWalk(Walk $walk): static
    {
        if ($this->walks->removeElement($walk)) {
            // set the owning side to null (unless already changed)
            if ($walk->getWalkGroup() === $this) {
                $walk->setWalkGroup(null);
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
            $review->setWalkGroup($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getWalkGroup() === $this) {
                $review->setWalkGroup(null);
            }
        }

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
            $comment->setGroup($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getGroup() === $this) {
                $comment->setGroup(null);
            }
        }

        return $this;
    }
}
