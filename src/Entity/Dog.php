<?php

namespace App\Entity;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Controller\DogController;
use App\DataPersister\DogDataPersister;
use App\DataPersister\DogImageDataPersister;
use App\Repository\DogRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
            uriTemplate: '/dogs/{id}',
            controller: DogController::class . '::deleteDog',
            security: "is_granted('DOG_DELETE', object)",
            securityMessage: "Vous ne pouvez supprimer que vos propres chiens"
        ),
        new Post(
            uriTemplate: '/dogs/{id}/image',
            denormalizationContext: ['groups' => ['dog:image']],
            validationContext: ['groups' => ['Default']],
            security: "is_granted('ROLE_USER')",
            securityMessage: "Vous ne pouvez uploader une image que pour vos chiens",
            deserialize: false,
            processor: DogImageDataPersister::class
        ),
    ]
)]
class Dog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['me:read', 'dog:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['dog:read', 'dog:write', 'me:read'])]
    private ?string $name = null;

 

    #[ORM\Column(length: 255)]
    #[Groups(['dog:read', 'dog:write', 'me:read'])]
    private ?string $gender = null;

    #[ORM\Column]
    #[Groups(['dog:read', 'dog:write', 'me:read'])]
    private ?\DateTimeImmutable $birthdate = null;

    #[ORM\ManyToOne(inversedBy: 'dogs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['me:read', 'dog:read'])]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['dog:read', 'me:read'])]
    private ?string $imageFilename = null;

    #[Groups(['dog:image'])]
    public ?UploadedFile $file = null;

    /**
     * @var Collection<int, Race>
     */
    #[ORM\ManyToMany(targetEntity: Race::class, inversedBy: 'dogs')]
    #[Groups(['dog:read', 'dog:write', 'me:read'])]
    private Collection $race;

    public function __construct()
    {
        $this->race = new ArrayCollection();
    }

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
     * @return Collection<int, Race>
     */
    public function getRace(): Collection
    {
        return $this->race;
    }

    public function addRace(Race $race): static
    {
        if (!$this->race->contains($race)) {
            $this->race->add($race);
        }

        return $this;
    }

    public function removeRace(Race $race): static
    {
        $this->race->removeElement($race);

        return $this;
    }
}
