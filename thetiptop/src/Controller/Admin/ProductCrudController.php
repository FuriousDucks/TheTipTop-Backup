<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('id')->setLabel('ID')->hideOnForm(),
            TextField::new('title')->setLabel('Titre'),
            TextField::new('description')->setLabel('Description'),
            MoneyField::new('price')->setCurrency('EUR')->setStoredAsCents(false)->setLabel('Prix'),
            ImageField::new('img')
                ->setBasePath('/images/products')
                ->onlyOnForms()
                ->onlyOnIndex()->setLabel('Image'),
            ImageField::new('img')
                ->setUploadDir('public/images/products')
                ->setBasePath('/images/products')
                ->setFormTypeOptions([
                    'required' => false,
                    'mapped' => false,
                ])
                ->onlyOnForms()->setLabel('Image'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Liste des produits')
            ->setPageTitle('new', 'Ajouter un produit')
            ->setPageTitle('edit', 'Modifier un produit')
            ->setPageTitle('detail', 'Détails du produit')
            ->setEntityLabelInPlural('Produits')
            ->setEntityLabelInSingular('Produit')
            ->setDefaultSort(['id' => 'DESC']);
    }

}
