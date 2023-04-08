<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Response;

class CustomerCrudController extends AbstractCrudController
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
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
            yield TextField::new('date_of_birth')->setLabel('Date de naissance')->setCssClass('datepicker'),

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


        return $actions
        ->add(Crud::PAGE_INDEX, $exportAction)
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
}
