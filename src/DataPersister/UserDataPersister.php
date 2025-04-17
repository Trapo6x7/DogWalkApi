<?php
// Ce fichier est a créer dans src/DataPersister
namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class UserDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly FileUploader $fileUploader,
        private readonly RequestStack $requestStack,
        private readonly Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if (str_contains($operation->getName(), 'image_post')) {
            $request = $this->requestStack->getCurrentRequest();

            /** @var User $user */
            $user = $this->security->getUser();

            if ($user) {
                if ($request && $request->files->has('file')) {
                    $file = $request->files->get('file');
                    // dd($file);

                    if ($file) {
                        $fileName = $this->fileUploader->upload($file);
                        $user->setImageFilename($fileName);
                        $user->setUpdatedAt(new \DateTimeImmutable());
                        // dd($user);
                        $this->entityManager->flush();
                    }
                }
                return $user;
            }
        }

        if ($data instanceof User) {
            if ($data->getPassword()) {
                // Vérifier si le mot de passe est déjà haché
                if (!$this->passwordHasher->isPasswordValid($data, $data->getPassword())) {
                    $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPassword());
                    $data->setPassword($hashedPassword);
                }
            }
        
            $this->entityManager->persist($data);
            $this->entityManager->flush();
        }

        return $data;
    }
}
