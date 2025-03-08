<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\GroupRequest;
use App\Entity\GroupRole;
use App\Entity\User;
use App\Repository\GroupRequestRepository;
use App\Repository\GroupRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class GroupRequestAdminDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        // private readonly GroupRequestRepository $groupRequestRepository,
        private readonly GroupRoleRepository $groupRoleRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GroupRequest
    {
        if ($data instanceof GroupRequest && $operation instanceof Patch){
            /** @var User */
            $connectedUser = $this->security->getUser();
            // $groupRequestConnectedUser = $this->groupRequestRepository->findGroupRequestByGroupAndUser($data->getWalkGroup(), $connectedUser);
            $groupRoleConnectedUser = $this->groupRoleRepository->findGroupRoleByGroupAndUser($data->getWalkGroup(), $connectedUser);
            if(!$groupRoleConnectedUser || $groupRoleConnectedUser->getRole() !== "ADMIN"){
                throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Access Denied: You do not have the necessary permissions.');
            } else {
                $connectedUserRole = $groupRoleConnectedUser->getRole();
                if($connectedUserRole === "ADMIN"){
                    $this->entityManager->persist($data);
                    $this->entityManager->flush();
                }
            }
           
        }

        return $data;   
        
    }
}