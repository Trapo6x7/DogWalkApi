<?php

namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\UploadedFileDto;
use App\Entity\Dog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DogImageDataPersister implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly ValidatorInterface $validator
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Dog
    {
        $request = $this->requestStack->getCurrentRequest();
        $file = $request->files->get('file');
        $dogId = $request->request->get('dog_id'); // ou en query param selon ton front

        $dog = $this->em->getRepository(Dog::class)->find($dogId);

        if (!$dog) {
            throw new \RuntimeException('Dog not found.');
        }

        if ($dog->getUser() !== $this->security->getUser()) {
            throw new \RuntimeException('Access denied.');
        }

        $dto = new UploadedFileDto();
        $dto->file = $file;

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new \RuntimeException((string) $errors);
        }

        $filename = uniqid('dog_') . '.' . $file->guessExtension();
        $file->move('uploads/dogs', $filename);

        $dog->setImageFilename($filename);
        $this->em->flush();

        return $dog;
    }
}
