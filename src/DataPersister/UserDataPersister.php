<?php
// Ce fichier est a créer dans src/DataPersister
namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
    
        if ($data->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPassword());
            $data->setPassword($hashedPassword);
            
        }

        if (!$data->getId()) {
            // L'entité est nouvelle, donc on peut la persister sans problème
            $this->entityManager->persist($data);
        }

        $this->entityManager->flush();

        return $data; // Toujours retourner un User
    }
}
