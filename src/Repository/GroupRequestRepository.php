<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\GroupRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupRequest>
 */
class GroupRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupRequest::class);
    }

    public function findGroupRequestByGroupAndUser(Group $group, User $user): ?GroupRequest
    {
        return $this->createQueryBuilder('grequest')
            ->andWhere('grequest.walkGroup = :group')
            ->andWhere('grequest.user = :user')
            ->setParameter('group', $group)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
//    /**
//     * @return GroupRequest[] Returns an array of GroupRequest objects
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

//    public function findOneBySomeField($value): ?GroupRequest
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
