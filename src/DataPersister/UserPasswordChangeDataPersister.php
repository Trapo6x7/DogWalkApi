<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordChangeDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if ($data instanceof User && $data->getPassword()) {
            // Hash le nouveau mot de passe s'il n'est pas déjà hashé
            if (!str_starts_with($data->getPassword(), '$2y$') || !str_starts_with($data->getPassword(), '$argon2')) {
                $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPassword());
                $data->setPassword($hashedPassword);
            }
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        }

        return $data;
    }
}