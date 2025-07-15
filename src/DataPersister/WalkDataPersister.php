<?php

namespace App\DataPersister;

use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Walk;
use App\Entity\Group;
use App\Entity\GroupRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class WalkDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Walk
    {
        // Vérifier que l'objet est bien une instance de Walk (création ou mise à jour)
        if ($data instanceof Walk) {
            $user = $this->security->getUser();

            if ($user) {
                $group = $data->getWalkGroup();

                if ($group) {
                    $groupRoleRepository = $this->entityManager->getRepository(GroupRole::class);
                    $groupRole = $groupRoleRepository->findOneBy([
                        'user' => $user,
                        'walkGroup' => $group,
                        'role' => ['MEMBER', 'CREATOR']
                    ]);

                    if ($groupRole) {
                        // Le groupe est déjà associé à la promenade, aucune action supplémentaire n'est nécessaire
                    } else {
                        throw new \Exception("L'utilisateur n'est pas membre du groupe sélectionné.");
                    }
                } else {
                    throw new \Exception("Le groupe à associer à la promenade est manquant.");
                }
            } else {
                throw new \Exception("L'utilisateur n'est pas connecté.");
            }

            // Persist et flush des données
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        }

        return $data;
    }
}