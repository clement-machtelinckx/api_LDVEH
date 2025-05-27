<?php

namespace App\Controller\Admin;

use App\Entity\Adventure;
use App\Entity\Book;
use App\Entity\Page;
use App\Entity\Choice;
use App\Entity\Monster;
use App\Entity\Adventurer;
use App\Entity\FightHistory;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(BookCrudController::class)->generateUrl());

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Api LDVEH');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Book', 'fas fa-box', Book::class);
        yield MenuItem::linkToCrud('Page', 'fas fa-box', Page::class);
        yield MenuItem::linkToCrud('Choice', 'fas fa-box', Choice::class);
        yield MenuItem::linkToCrud('Adventurer', 'fas fa-box', Adventurer::class);
        yield MenuItem::linkToCrud('Monster', 'fas fa-box', Monster::class);
        yield MenuItem::linkToCrud('Adventure', 'fas fa-box', Adventure::class);
        yield MenuItem::linkToCrud('Fight History', 'fas fa-box', FightHistory::class);
        yield MenuItem::LinkToCrud('User', 'fas fa-users', User::class)
            ->setPermission('ROLE_ADMIN');
    }
}
