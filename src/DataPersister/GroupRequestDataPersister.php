<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\GroupRequest;
use App\Entity\User;
use App\Repository\GroupRoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class GroupRequestDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        // private readonly GroupRequestRepository $groupRequestRepository,
        private readonly GroupRoleRepository $groupRoleRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GroupRequest
    {
        if ($data instanceof GroupRequest && $operation instanceof Post) {
            /** @var User */
            $connectedUser = $this->security->getUser();
            if (!$connectedUser) {
                throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Seuls les utilisateurs connectés peuvent faire une demande de groupe');
            } else {
                $data->setStatus(false);
                $this->entityManager->persist($data);
                $this->entityManager->flush();
            }
        }
        return $data;
    }
}
