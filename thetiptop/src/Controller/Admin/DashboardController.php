<?php

namespace App\Controller\Admin;

use App\Entity\Winner;
use App\Entity\Product;
use App\Entity\Customer;
use App\Entity\Employee;
use App\Repository\ProductRepository;
use App\Repository\TicketRepository;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\WinnerRepository;
use App\Repository\CustomerRepository;
use App\Controller\Admin\WinnerCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
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
            return $this->redirectToRoute('statistics');
        } else {
            return $this->redirect($adminUrlGenerator->setController(WinnerCrudController::class)->generateUrl());
        }
    }

    #[Route('/admin/statistiques', name: 'statistics')]
    public function statistics(ChartBuilderInterface $chartBuilder, WinnerRepository $winnerRepository, CustomerRepository $customerRepository, ProductRepository $productRepository, TicketRepository $ticketRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'chart' => $this->countUserPerProduct($chartBuilder, $winnerRepository, $productRepository),
            'countUserPerProduct' => $this->countUserPerProduct($chartBuilder, $winnerRepository, $productRepository),
            'usedandunusedtickets' => $this->countOfTicketUsed($chartBuilder, $ticketRepository),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/images/app/logo.png" alt="Thétiptop Logo" class="logo" style="width: 150px;object-fit: contain;">')
            ->setFaviconPath('/images/app/logo.png')
            ->setLocales([
                'en' => '🇬🇧 English',
                'fr' => '🇫🇷 Français',
            ]);
    }

    public function configureMenuItems(): iterable
    {
        // yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
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

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('admin')
            ->addCssFile('build/css/admin.css');
    }

    public function countUserPerProduct(ChartBuilderInterface $chartBuilder, WinnerRepository $winnerRepository, ProductRepository $productRepository)
    {
        $products = $productRepository->countUserPerProduct();
        $titles = array_map(function ($product) {
            return $product['title'];
        }, $products);
        $counts = array_map(function ($product) {
            return $product['winners'];
        }, $products);
        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels' => $titles,
            'datasets' => [
                [
                    'label' => 'Statistiques',
                    'backgroundColor' => ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(153, 102, 255)', 'rgb(255, 159, 64)', 'rgb(201, 203, 207)'],
                    'borderColor' => ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(153, 102, 255)', 'rgb(255, 159, 64)', 'rgb(201, 203, 207)'],
                    'data' => $counts,
                ],
            ],
        ]);

        $chart->setOptions([
            'responsive' => true,
            'maintainAspectRatio'=> false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Nombre de gagnants par produit',
                ],
                'zoom' => [
                    'zoom' => [
                        'wheel' => ['enabled' => true],
                        'pinch' => ['enabled' => true],
                        'mode' => 'xy',
                    ],
                ],
            ],
        ]);

        return $chart;
    }
    public function countOfTicketUsed(ChartBuilderInterface $chartBuilder, TicketRepository $ticketRepository)
    {

        $used = $ticketRepository->usedTickets();
        $unused = $ticketRepository->unusedTickets();

        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels' => ['Utilisés', 'Non utilisés'],
            'datasets' => [
                [
                    'label' => 'Statistiques',
                    'backgroundColor' => ['rgb(255, 99, 132)', 'rgb(54, 162, 235)'],
                    'borderColor' => ['rgb(255, 99, 132)', 'rgb(54, 162, 235)'],
                    'data' => [$used, $unused]
                ],
            ],
        ]);

        $chart->setOptions([
            'responsive' => true,
            'maintainAspectRatio'=> false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Nombre de tickets utilisés et non utilisés',
                ],
                'zoom' => [
                    'zoom' => [
                        'wheel' => ['enabled' => true],
                        'pinch' => ['enabled' => true],
                        'mode' => 'xy',
                    ],
                ],
            ],
        ]);

        return $chart;
    }
}
