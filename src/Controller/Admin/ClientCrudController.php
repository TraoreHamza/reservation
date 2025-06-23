<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ClientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Client::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Client')
            ->setEntityLabelInPlural('Clients')
            ->setPageTitle("index", 'Gestion des clients')
            ->setPageTitle("new", 'Ajouter un client')
            ->setPageTitle("edit", 'Modifier un client')
            ->setPageTitle("detail", "Détail du client");
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('address');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')
                ->setHelp('Nom du client.'),
            TextField::new('address')
                ->setHelp('Adresse du client.'),
        ];
    }
}
