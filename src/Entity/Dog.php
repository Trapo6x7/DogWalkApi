<?php

namespace App\Entity;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\DataPersister\DogDataPersister;
use App\Repository\DogRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: DogRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['dog:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['dog:read']],
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['dog:write']],
            security: "is_granted('ROLE_USER')",
            processor: DogDataPersister::class,
            securityMessage: "Seuls les utilisateurs connectés peuvent créer des chiens"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['dog:write']],
            security: "is_granted('DOG_EDIT', object)",
            securityMessage: "Vous ne pouvez modifier que vos propres chiens"
        ),
        new Delete(
            security: "is_granted('DOG_DELETE', object)",
            securityMessage: "Vous ne pouvez supprimer que vos propres chiens"
        ),
    ]
)]
class Dog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['me:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['dog:read', 'dog:write', 'me:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['dog:read', 'dog:write', 'me:read'])]
    private ?string $race = null;

    #[ORM\Column(length: 255)]
    #[Groups(['dog:read', 'dog:write', 'me:read'])]
    private ?string $gender = null;

    #[ORM\Column]
    #[Groups(['dog:read', 'dog:write', 'me:read'])]
    private ?\DateTimeImmutable $birthdate = null;

    #[ORM\ManyToOne(inversedBy: 'dogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(string $race): static
    {
        $this->race = $race;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
