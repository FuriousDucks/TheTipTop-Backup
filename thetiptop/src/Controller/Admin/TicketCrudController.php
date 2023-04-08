<?php

namespace App\Controller\Admin;

use App\Entity\Ticket;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TicketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ticket::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des tickets')
            ->setPageTitle('new', 'Ajouter un ticket')
            ->setPageTitle('edit', 'Modifier un ticket')
            ->setPageTitle('detail', 'Détails du ticket')
            ->setEntityLabelInPlural('Tickets')
            ->setEntityLabelInSingular('Ticket')
            ->setDefaultSort(['id' => 'DESC']);
    }
}
