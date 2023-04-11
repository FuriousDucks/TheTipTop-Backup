<?php

namespace App\Controller\Admin;

use App\Entity\Employee;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class EmployeeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Employee::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            yield TextField::new('email')->setLabel('Email'),
            yield BooleanField::new('isVerified')->setLabel('Email vérifié'),
            yield ChoiceField::new('roles')->setLabel('Rôle')->setChoices([
                'Employé' => 'ROLE_EMPLOYEE',
                'Administrateur' => 'ROLE_ADMIN',
            ])->allowMultipleChoices(),
            yield TextField::new('firstname')->setLabel('Prénom'),
            yield TextField::new('lastname')->setLabel('Nom'),
            yield ChoiceField::new('job')->setLabel('Poste')->setChoices([
                'Employé' => 'employee',
                'Directeur' => 'admin',
            ]),
        ];
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des employés')
            ->setPageTitle('new', 'Ajouter un employé')
            ->setPageTitle('edit', 'Modifier un employé')
            ->setPageTitle('detail', 'Détails de l\'employé')
            ->setEntityLabelInPlural('Employés')
            ->setEntityLabelInSingular('Employé')
            ->setDefaultSort(['id' => 'DESC']);
    }

}
