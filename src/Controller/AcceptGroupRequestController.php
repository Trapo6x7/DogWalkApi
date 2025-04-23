<?php

namespace App\Controller;

use App\Entity\GroupRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AcceptGroupRequestController extends AbstractController
{
    public function __invoke(GroupRequest $data, EntityManagerInterface $em): GroupRequest
    {
        $user = $this->getUser();
        $group = $data->getWalkGroup();

        if ($group->getGroupRoles()->filter(fn($role) => $role->getUser() === $user && $role->getRole() === 'creator')->isEmpty()) {
            throw new AccessDeniedHttpException('Seul le créateur du groupe peut accepter une demande.');
        }

        $data->setStatus(true); 

        $em->persist($data);
        $em->flush();

        return $data;
    }
}
