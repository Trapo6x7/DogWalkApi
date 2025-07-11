<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\AcceptGroupRequestController;
use App\DataPersister\GroupRequestAdminDataPersister;
use App\DataPersister\GroupRequestDataPersister;
use App\DataPersister\GroupRoleDataPersister;
use App\Repository\GroupRequestRepository;
use App\State\Provider\AdminGroupsProvider;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GroupRequestRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['groupRequest:read']],
            security: "is_granted('GROUPREQUEST_READ', object)",
            securityMessage: "Seuls les admins peuvent voir les demandes"
        ),
        new GetCollection(
            uriTemplate: '/groupsRequests',
            normalizationContext: ['groups' => ['groupRequest:readAll']],
            security: "is_granted('GROUPREQUESTALL_READ', object)",
            securityMessage: "Seuls les admins peuvent voir les demandes",
            provider: AdminGroupsProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => ['groupRequest:write']],
            security: "is_granted('ROLE_USER')",
            processor: GroupRequestDataPersister::class,
            securityMessage: "Seuls les utilisateurs connectés peuvent faire une demande"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['groupRequest:patch']],
            security: "is_granted('GROUPREQUEST_READ', object)",
            processor: GroupRequestAdminDataPersister::class,
            securityMessage: "Seuls les admins peuvent accepter les demandes"
        ),
        new Patch(
            uriTemplate: '/group_requests/{id}/accept',
            controller: AcceptGroupRequestController::class,
            security: "is_granted('ROLE_USER')",
            denormalizationContext: ['groups' => ['groupRequest:accept']],
            name: 'accept_group_request'
        ),
        new Delete(
            security: "is_granted('ROLE_USER')",
            securityMessage: "Seul le créateur du groupe ou un admin peut refuser une demande."
        )
    ]
)]
class GroupRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['groupRequest:read', 'groupRequest:readAll'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'groupRequests')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['groupRequest:write','groupRequest:read','groupRequest:readAll', 'me:read'])]
    private ?Group $walkGroup = null;

    #[ORM\ManyToOne(inversedBy: 'groupRequests')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['groupRequest:write','groupRequest:read','groupRequest:readAll', 'group:details'])]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups(['groupRequest:read','groupRequest:readAll'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['groupRequest:patch','groupRequest:read','groupRequest:readAll', 'me:read', 'group:details'])]
    private ?bool $status = false;

    public function __construct(DateTimeImmutable $createdAt = new DateTimeImmutable(), DateTimeImmutable $updatedAt = new DateTimeImmutable())
    {
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
    
    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
