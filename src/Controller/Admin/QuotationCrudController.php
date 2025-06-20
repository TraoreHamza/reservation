<?php

namespace App\Controller\Admin;

use App\Entity\Quotation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class QuotationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Quotation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Devis')
            ->setEntityLabelInPlural('Devis')
            ->setPageTitle("index", 'Gestion des devis')
            ->setPageTitle("new", 'Ajouter un devis')
            ->setPageTitle("edit", 'Modifier un devis')
            ->setPageTitle("detail", "Détail du devis");
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('room')
                ->setLabel('Salle réservée')
                ->setHelp('Description de la salle incluse dans le devis.'),
            TextField::new('client')
                ->setLabel('Client')
                ->setHelp('Détails du client pour lequel le devis est préparé.'),
            TextField::new('price')
                ->setLabel('Montant de la réservation')
                ->setHelp('Prix total du devis.'),
            TextField::new('date')
                ->setLabel('Date(s) de réservation')
                ->setHelp('Date de validité du devis.'),
            TextField::new('created_at')
                ->setLabel('Créé-le')
                ->setHelp('Date de création du devis.')
                ->onlyOnDetail(),
        ];
    }
}
