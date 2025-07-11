<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\GroupRole;
use App\Entity\User;
use App\Repository\GroupRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class GroupRoleDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly GroupRoleRepository $groupRoleRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GroupRole
    {
        if ($data instanceof GroupRole && $operation instanceof Post){
            /** @var User */
            $connectedUser = $this->security->getUser();
            $groupRoleConnectedUser = $this->groupRoleRepository->findGroupRoleByGroupAndUser($data->getWalkGroup(), $connectedUser);
            if(!$groupRoleConnectedUser || $groupRoleConnectedUser->getRole() !== "CREATOR"){
                throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Access Denied: You do not have the necessary permissions.');
            } else {
                $connectedUserRole = $groupRoleConnectedUser->getRole();
                if($connectedUserRole === "CREATOR"){
                    $this->entityManager->persist($data);
                    $this->entityManager->flush();
                }
            }
           
        }

        return $data;   
        
    }
}