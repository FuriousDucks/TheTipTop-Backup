<?php

namespace App\Controller\Admin;

use App\Entity\Winner;
use App\Entity\Product;
use App\Entity\Customer;
use App\Entity\Employee;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\CustomerCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function admin(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirect($adminUrlGenerator->setController(CustomerCrudController::class)->generateUrl());
        } else {
            return $this->redirect($adminUrlGenerator->setController(WinnerCrudController::class)->generateUrl());
        }
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Thétiptop')
            ->setFaviconPath('/images/app/logo.png')
            ->setLocales([
                'en' => '🇬🇧 English',
                'fr' => '🇫🇷 Français',
            ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Gagnants', 'fas fa-list', Winner::class)->setPermission('ROLE_EMPLOYEE');
        yield MenuItem::linkToCrud('Clients', 'fas fa-user', Customer::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Produits', 'fas fa-list', Product::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Employés', 'fas fa-list', Employee::class)->setPermission('ROLE_ADMIN');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getFirstName() . ' ' . $user->getLastName())
            ->setGravatarEmail($this->getUser()->getEmail());
    }
}
