<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\DataPersister\CommentDataPersister;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiFilter(SearchFilter::class, properties: ['group' => 'exact'])]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['comment:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['comment:read']]
        ),
        new Post(
            processor: CommentDataPersister::class,
            denormalizationContext: ['groups' => ['comment:write']],
            normalizationContext: ['groups' => ['comment:read']],
            security: "is_granted('ROLE_USER')"
        )
    ]
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['comment:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read', 'comment:write'])]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['comment:read', 'comment:write'])]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read', 'comment:write'])]
    private ?Group $group = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): static
    {
        $this->group = $group;

        return $this;
    }
}
