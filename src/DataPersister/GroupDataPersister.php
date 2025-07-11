<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Dog;
use App\Entity\Group;
use App\Entity\GroupRole;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GroupDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Group
    {
        if ($data instanceof Group && $operation instanceof Post) {
            /** @var User $creator */
            $creator = $this->security->getUser();
            // Lier le créateur au groupe
            $data->setCreator($creator);

            $groupRole = new GroupRole();
            $groupRole->setUser($creator);
            $groupRole->setRole("CREATOR");
            $groupRole->setWalkGroup($data);

            $this->entityManager->persist($groupRole);
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        }

        return $data;
    }
}