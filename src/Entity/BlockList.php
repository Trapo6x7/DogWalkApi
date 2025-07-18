<?php

namespace App\Entity;

use App\Repository\BlockListRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BlockListRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['blockList:read', 'user:read']],
    denormalizationContext: ['groups' => ['blockList:write']],
    operations: [
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_USER')"),
        new Delete(security: "is_granted('ROLE_USER') and object.getBlocker() == user")
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['blocker' => 'exact', 'blocked' => 'exact'])]
class BlockList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['blockList:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'blockLists')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['blockList:read', 'blockList:write', 'user:read'])]
    private ?User $blocker = null;

    #[ORM\ManyToOne(inversedBy: 'blockLists')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['blockList:read', 'blockList:write', 'user:read'])]
    private ?User $blocked = null;

    #[ORM\Column]
    #[Groups(['blockList:read'])]
    private ?DateTimeImmutable $createdAt = null;

    public function __construct(DateTimeImmutable $createdAt = new DateTimeImmutable())
    {
        $this->createdAt = $createdAt;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlocker(): ?User
    {
        return $this->blocker;
    }

    public function setBlocker(?User $blocker): static
    {
        $this->blocker = $blocker;

        return $this;
    }

    public function getBlocked(): ?User
    {
        return $this->blocked;
    }

    public function setBlocked(?User $blocked): static
    {
        $this->blocked = $blocked;

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
}
