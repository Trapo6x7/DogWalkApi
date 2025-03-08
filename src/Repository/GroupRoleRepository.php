<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\GroupRole;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupRole>
 */
class GroupRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupRole::class);
    }

    public function findGroupRoleByGroupAndUser(Group $group, User $user): ?GroupRole
    {
        return $this->createQueryBuilder('gr')
            ->andWhere('gr.walkGroup = :group')
            ->andWhere('gr.user = :user')
            ->setParameter('group', $group)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return GroupRole[] Returns an array of GroupRole objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GroupRole
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
