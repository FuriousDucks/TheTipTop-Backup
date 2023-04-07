<?php

namespace App\Controller\Admin;

use App\Entity\Winner;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;

class WinnerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Winner::class;
    }
    

    public function configureFields(string $pageName): iterable
    {
        return [
            yield AssociationField::new('product'),
            yield AssociationField::new('ticket'),
            yield DateField::new('dateofdraw'),
            yield AssociationField::new('customer'),
        ];
    }

}
