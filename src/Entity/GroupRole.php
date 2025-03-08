<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\DataPersister\GroupRoleDataPersister;
use App\Repository\GroupRoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GroupRoleRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['groupeRole:read']],
            security: "is_granted('ROLE_USER')",
            securityMessage: "Seuls les utilisateurs connectés peuvent voir les roles d'un groupe"
        ),
        new Post(
            denormalizationContext: ['groups' => ['groupRole:write']],
            security: "is_granted('ROLE_USER')",
            processor: GroupRoleDataPersister::class,
            securityMessage: "Seuls les utilisateurs connectés peuvent créer des groupes"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['groupRole:patch']],
            security: "is_granted('GROUPROLE_EDIT', object)",
            securityMessage: "Vous ne pouvez modifier que vos propres groupes"
        ),
        new Delete(
            security: "is_granted('GROUP_DELETE', object)",
            securityMessage: "Vous ne pouvez supprimer que vos propres groupes"
        ),
    ]
)]
class GroupRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['groupeRole:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['group:details', 'groupRole:patch', 'groupeRole:read'])]
    private ?string $role = null;

    #[ORM\ManyToOne(inversedBy: 'groupRoles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['group:details', 'groupRole:write', 'groupeRole:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'groupRoles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['groupRole:write', 'groupeRole:read'])]
    private ?Group $walkGroup = null;

    public function __construct(string $role = "MEMBER")
    {
        $this->role = $role;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

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

    public function getWalkGroup(): ?Group
    {
        return $this->walkGroup;
    }

    public function setWalkGroup(?Group $walkGroup): static
    {
        $this->walkGroup = $walkGroup;

        return $this;
    }
}
