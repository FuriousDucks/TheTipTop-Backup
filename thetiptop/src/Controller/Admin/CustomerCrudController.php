<?php

namespace App\Controller\Admin;

use App\Entity\Winner;
use App\Entity\Customer;
use App\Repository\WinnerRepository;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CustomerCrudController extends AbstractCrudController
{
    private CustomerRepository $customerRepository;
    private WinnerRepository $winnerRepository;
    private ProductRepository $productRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(CustomerRepository $customerRepository, WinnerRepository $winnerRepository, ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->customerRepository = $customerRepository;
        $this->winnerRepository = $winnerRepository;
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }
    public static function getEntityFqcn(): string
    {
        return Customer::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            yield EmailField::new('email')->setLabel('Email'),
            yield TextField::new('firstname')->setLabel('Prénom'),
            yield TextField::new('lastname')->setLabel('Nom'),
            yield TelephoneField::new('phone')->setLabel('Téléphone'),
            yield TextField::new('address')->setLabel('Adresse'),
            yield TextField::new('date_of_birth')->setLabel('Date de naissance'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des clients')
            ->setPageTitle('new', 'Ajouter un client')
            ->setPageTitle('edit', 'Modifier un client')
            ->setPageTitle('detail', 'Détails du client')
            ->setEntityLabelInPlural('Clients')
            ->setEntityLabelInSingular('Client')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $exportAction = Action::new('export', 'Exporter', 'fas fa-file-export')
            ->linkToCrudAction('export')
            ->addCssClass('btn btn-primary')
            ->setHtmlAttributes(['target' => '_blank'])->createAsGlobalAction();

        $oneyearbutton = Action::new('oneyear', '1 an de thé', 'fas fa-gift')
            ->linkToCrudAction('oneyear')
            ->addCssClass('btn btn-primary')->createAsGlobalAction();


        return $actions
        ->add(Crud::PAGE_INDEX, $exportAction)
        ->add(Crud::PAGE_INDEX, $oneyearbutton)
        ->remove(Crud::PAGE_INDEX, Action::NEW)
        ->remove(Crud::PAGE_INDEX, Action::EDIT)
        ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return Filters::new()
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('phone')
        ;
    }

    public function export()
    {
        $customers = $this->customerRepository->findAll();
        $csv = fopen('php://memory', 'w');
        fputcsv($csv, ['Email', 'Prénom', 'Nom', 'Téléphone', 'Adresse', 'Date de naissance']);
        foreach ($customers as $customer) {
            fputcsv($csv, [$customer->getEmail(), $customer->getFirstName(), $customer->getLastName(), $customer->getPhone(), $customer->getAddress(), $customer->getDateOfBirth()]);
        }
        rewind($csv);
        $response = new Response(mb_convert_encoding(stream_get_contents($csv), 'UTF-16LE', 'UTF-8'));
        fclose($csv);
        $response->headers->set('Pragma', 'public');
        $response->headers->set('charset', 'UTF-8');
        $response->headers->set('Content-enconding', 'UTF-8');
        $response->headers->set('Content-Type', 'text/csv', );
        $response->headers->set('Content-Disposition', 'attachment; filename="customers.csv"');
        return $response;
    }

    public function oneyear()
    {
        $product = $this->productRepository->findOneBy(['title' => '1 an de Thé détox']);
        $exists = $this->winnerRepository->findOneBy(['product' => $product]);
        if(count($this->winnerRepository->findAll()) === 0) {
            $this->addFlash('danger', 'Il n\'y a pas encore de gagnant !');
            return $this->redirect($this->generateUrl('admin', [
                'action' => 'index',
                'entity' => 'Winner',
            ]));
        } elseif (!$product) {
            $this->addFlash('danger', 'Le produit 1 an de Thé détox n\'existe pas !, veuillez le créer !');
            return $this->redirect($this->generateUrl('admin', [
                'action' => 'index',
                'entity' => 'Product',
            ]));
        }

        if(!$exists) {
            $winners = $this->winnerRepository->findAllDistinct();
            $ids = [];

            foreach ($winners as $winner) {
                $ids[] = $winner->getCustomer()->getId();
            }

            $customer = $this->customerRepository->findOneBy(['id' => $ids[array_rand($ids)]]);

            $winner = new Winner();
            $winner->setCustomer($customer);
            $winner->setDateOfDraw(new \DateTime());
            $winner->setProduct($product);
            $winner->setRecovered(false);
            $this->entityManager->persist($winner);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le gagnant a bien été tiré au sort !');

            return $this->redirect($this->generateUrl('admin', [
                'action' => 'detail',
                'entity' => 'Winner',
                'id' => $winner->getId(),
            ]));
        } else {
            $this->addFlash('danger', 'Le gagnant a déjà été tiré au sort !');
            return $this->redirect($this->generateUrl('admin', [
                'action' => 'index',
                'entity' => 'Winner',
            ]));
        }
    }
}
