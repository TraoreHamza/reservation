<?php

namespace App\Controller\Admin;

use App\Entity\Equipment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class EquipmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Equipment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Équipement')
            ->setEntityLabelInPlural('Équipements')
            ->setPageTitle("index", 'Gestion des équipements')
            ->setPageTitle("new", 'Ajouter un équipement')
            ->setPageTitle("edit", 'Modifier un équipement')
            ->setPageTitle("detail", "Detail de l'équipement")
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')
                ->setHelp('Name of the equipment.'),
            TextField::new('type')
                ->setHelp('Type of the equipment (e.g., projector, microphone).'),



        ];
    }
}
