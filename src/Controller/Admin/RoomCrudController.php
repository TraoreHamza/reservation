<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class RoomCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Room::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Salle')
            ->setEntityLabelInPlural('Salles')
            ->setPageTitle("index", 'Gestion des salles')
            ->setPageTitle("new", 'Ajouter une salle')
            ->setPageTitle("edit", 'Modifier une salle')
            ->setPageTitle("detail", "Détail de la salle");
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
                ->setLabel('Nom')
                ->setHelp('Nom de la salle.'),
            TextEditorField::new('description')
                ->setLabel('Description'),
            ImageField::new('image')
                ->setLabel('Sélectionnez une image')
                ->setBasePath('medias/images/')
                ->setUploadDir('public/medias/images')
                ->setUploadedFileNamePattern('[slug]-[contenthash].[extension]'),
            IntegerField::new('capacity')
                ->setLabel('Capacité')
                ->setHelp('Capacité maximale de la salle.'),
            BooleanField::new('isAvailable')
                ->setLabel('Disponibilité')
                ->setHelp('Indique si la salle est disponible pour réservation.'),
            AssociationField::new('equipments')
                ->setLabel('Équipements')
                ->renderAsNativeWidget()
                ->setHelp('Nom de l\'équipement associé à la réservation.'),
            AssociationField::new('options')
                ->setLabel('Options')
                ->renderAsNativeWidget()
                ->setHelp('Nom de l\'option associée à la réservation.'),
            AssociationField::new('location')
                ->renderAsNativeWidget()
                ->setLabel('Emplacement')
                ->setHelp('Emplacement de la salle'),

        ];
    }
}
