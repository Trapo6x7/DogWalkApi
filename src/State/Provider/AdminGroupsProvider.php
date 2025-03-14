<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Group;
use App\Entity\GroupRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AdminGroupsProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();

        if (!$user || !is_object($user)) {
            return [];
        }
        
        return $this->entityManager->getRepository(GroupRequest::class)
        ->createQueryBuilder('grq')
        ->innerJoin('grq.walkGroup', 'g')
        ->innerJoin('g.groupRoles', 'gr')
        ->where('gr.user = :user')
        ->andWhere('gr.role = :role')
        ->setParameter('user', $user)
        ->setParameter('role', 'ADMIN')
        ->getQuery()
        ->getResult();
    }
}
