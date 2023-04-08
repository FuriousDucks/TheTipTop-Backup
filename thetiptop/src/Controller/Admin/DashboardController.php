<?php

namespace App\Controller\Admin;

use App\Entity\Winner;
use App\Entity\Product;
use App\Entity\Customer;
use App\Entity\Employee;
use App\Repository\CustomerRepository;
use App\Repository\WinnerRepository;
use Symfony\UX\Chartjs\Model\Chart;
use App\Controller\Admin\WinnerCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\CustomerCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
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

    #[Route('/admin/statistiques', name: 'statistics')]
    public function statistics(ChartBuilderInterface $chartBuilder, WinnerRepository $winnerRepository, CustomerRepository $customerRepository): Response
    {
        /* $chart = $chartBuilder->createChart(Chart::TYPE_PIE);

        $chart->setData([
            'labels' => ['Gagnants', 'Clients'],
            'datasets' => [
                [
                    'label' => 'Statistiques',
                    'backgroundColor' => ['rgb(255, 99, 132)', 'rgb(54, 162, 235)'],
                    'borderColor' => ['rgb(255, 99, 132)', 'rgb(54, 162, 235)'],
                    'data' => [$winnerRepository->count([]), $customerRepository->count([])],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'yAxes' => [
                    [
                        'ticks' => [
                            'beginAtZero' => true,
                        ],
                    ],
                ],
            ],
            'legend' => [
                'display' => true,
            ],
            'title' => [
                'display' => true,
                'text' => 'Statistiques',
            ],
            'responsive' => true,
            'plugins' => [
                'zoom' => [
                    'zoom' => [
                        'wheel' => ['enabled' => true],
                        'pinch' => ['enabled' => true],
                        'mode' => 'xy',
                    ],
                ],
            ],
        ]); */
        
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);
        return $this->render('admin/dashboard.html.twig', [
            'chart' => $chart,
        ]);
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
        yield MenuItem::linkToUrl('Statistiques', 'fa-solid fa-chart-gantt', '/admin/statistiques')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Gagnants', 'fas fa-gift', Winner::class)->setPermission('ROLE_EMPLOYEE');
        yield MenuItem::linkToCrud('Clients', 'fas fa-user', Customer::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Produits', 'fab fa-product-hunt', Product::class)->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Employés', 'fas fa-users', Employee::class)->setPermission('ROLE_ADMIN');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getFirstName() . ' ' . $user->getLastName())
            ->setGravatarEmail($this->getUser()->getEmail());
    }
}
