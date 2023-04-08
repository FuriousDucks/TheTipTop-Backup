<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextField::new('description'),
            MoneyField::new('price')->setCurrency('EUR')->setStoredAsCents(false),
            ImageField::new('img')
                ->setBasePath('/images/products')
                ->onlyOnForms()
                ->onlyOnIndex(),
            ImageField::new('img')
                ->setUploadDir('public/images/products')
                ->setBasePath('/images/products')
                ->setFormTypeOptions([
                    'required' => false,
                    'mapped' => false,
                ])
                ->onlyOnForms(),
        ];
    }

}
