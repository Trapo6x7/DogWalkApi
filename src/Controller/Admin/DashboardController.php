<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Dog;
use App\Entity\User;
use App\Entity\Walk;
use App\Entity\Group;
use App\Entity\GroupRequest;
use App\Entity\GroupRole;
use App\Entity\Race;
use App\Entity\Report;
use App\Entity\Review;
use App\Entity\Comment;
use App\Entity\BlockList;

class DashboardController extends AbstractDashboardController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Fetch entity manager
        $em = $this->doctrine->getManager();
        // Fetch total counts for entities via QueryBuilder
        $dogCount = $em->getRepository(Dog::class)->createQueryBuilder('e')->select('count(e.id)')->getQuery()->getSingleScalarResult();
        $userCount = $em->getRepository(User::class)->createQueryBuilder('e')->select('count(e.id)')->getQuery()->getSingleScalarResult();
        $walkCount = $em->getRepository(Walk::class)->createQueryBuilder('e')->select('count(e.id)')->getQuery()->getSingleScalarResult();
        $groupCount = $em->getRepository(Group::class)->createQueryBuilder('e')->select('count(e.id)')->getQuery()->getSingleScalarResult();
        $groupRequestCount = $em->getRepository(GroupRequest::class)->createQueryBuilder('e')->select('count(e.id)')->getQuery()->getSingleScalarResult();
        $groupRoleCount = $em->getRepository(GroupRole::class)->createQueryBuilder('e')->select('count(e.id)')->getQuery()->getSingleScalarResult();
        $raceCount = $em->getRepository(Race::class)->createQueryBuilder('e')->select('count(e.id)')->getQuery()->getSingleScalarResult();
        $reportCount = $em->getRepository(Report::class)->createQueryBuilder('e')->select('count(e.id)')->getQuery()->getSingleScalarResult();
        $reviewCount = $em->getRepository(Review::class)->createQueryBuilder('e')->select('count(e.id)')->getQuery()->getSingleScalarResult();
        $commentCount = $em->getRepository(Comment::class)->createQueryBuilder('e')->select('count(e.id)')->getQuery()->getSingleScalarResult();
        $blockListCount = $em->getRepository(BlockList::class)->createQueryBuilder('e')->select('count(e.id)')->getQuery()->getSingleScalarResult();

        return $this->render('admin/dashboard.html.twig', [
            'counts' => [
                'dogs' => $dogCount,
                'users' => $userCount,
                'walks' => $walkCount,
                'groups' => $groupCount,
                'groupRequests' => $groupRequestCount,
                'groupRoles' => $groupRoleCount,
                'races' => $raceCount,
                'reports' => $reportCount,
                'reviews' => $reviewCount,
                'comments' => $commentCount,
                'blockLists' => $blockListCount,
            ],
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('DogWalk Admin');
    }

    public function configureMenuItems(): iterable
    {
        // yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Chiens', 'fas fa-dog', Dog::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Promenades', 'fas fa-walking', Walk::class);
        yield MenuItem::linkToCrud('Groupes', 'fas fa-users', Group::class);
        yield MenuItem::linkToCrud('Demandes de groupe', 'fas fa-envelope', GroupRequest::class);
        yield MenuItem::linkToCrud('Rôles de groupe', 'fas fa-user-tag', GroupRole::class);
        yield MenuItem::linkToCrud('Races', 'fas fa-paw', Race::class);
        yield MenuItem::linkToCrud('Rapports', 'fas fa-flag', Report::class);
        yield MenuItem::linkToCrud('Avis', 'fas fa-star', Review::class);
        yield MenuItem::linkToCrud('Commentaires', 'fas fa-comment', Comment::class);
        yield MenuItem::linkToCrud('Liste de blocage', 'fas fa-ban', BlockList::class);
    }
}
