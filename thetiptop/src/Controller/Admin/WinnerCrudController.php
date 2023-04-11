<?php

namespace App\Controller\Admin;

use App\Entity\Winner;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class WinnerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Winner::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            yield AssociationField::new('product')->setDisabled(true)->setLabel('Produit'),
            yield AssociationField::new('ticket')->setDisabled(true),
            yield DateField::new('dateofdraw')->setDisabled(true)->setLabel('Date du tirage'),
            yield AssociationField::new('customer')->setDisabled(true)->setLabel('Client'),
            yield BooleanField::new('recovered')->setLabel('Gain récupéré')->setHelp('Le gain a-t-il été récupéré ?')->onlyOnForms(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des gagnants')
            ->setPageTitle('new', 'Ajouter un gagnant')
            ->setPageTitle('edit', 'Modifier un gagnant')
            ->setPageTitle('detail', 'Détails du gagnant')
            ->setEntityLabelInPlural('Gagnants')
            ->setEntityLabelInSingular('Gagnant')
            ->setSearchFields(['product', 'ticket', 'dateofdraw', 'customer', 'recovered'])
            ->setDefaultSort(['id' => 'DESC']);
    }
}
