<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ReviewRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Post(
            denormalizationContext: ['groups' => ['review:write']],
            validationContext: ['groups' => ['Default']],
            security: "is_granted('ROLE_USER')",
        ),
        new Get(
            normalizationContext: ['groups' => ['review:read']],
            security: "is_granted('ROLE_USER')",
        ),
    ]
)]
#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['review:write', 'review:read', 'user:read', 'me:read', 'user:read'])]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: 'Veuillez mettre une note de {{ min }} a {{ max }}.'
    )]
    private ?int $rating = null;

    #[ORM\Column(length: 255)]
    #[Groups(['review:write', 'review:read', 'user:read', 'me:read', 'user:read'])]
    private ?string $comment = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['review:read', 'review:write'])]
    private ?User $user = null;

    public function __construct(DateTimeImmutable $createdAt = new DateTimeImmutable())
    {
        $this->createdAt = $createdAt;

    }
    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }
}
