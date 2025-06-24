<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

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
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom de la salle'),
            IntegerField::new('capacity', 'Capacité'),
            TextField::new('description', 'Description'),
            MoneyField::new('price', 'Prix')->setCurrency('EUR'),
            MoneyField::new('dailyRate', 'Prix journalier')->setCurrency('EUR'),
            BooleanField::new('isAvailable', 'Disponible'),
            TextField::new('image', 'Image'),
            AssociationField::new('location', 'Localisation'),
            BooleanField::new('luminosity', 'Luminosité naturelle')
                ->setHelp('La salle dispose-t-elle d\'une luminosité naturelle ?'),
            BooleanField::new('pmr_access', 'Accessibilité PMR')
                ->setHelp('La salle est-elle accessible aux personnes à mobilité réduite ?'),
            TextareaField::new('ergonomics_notes', 'Notes ergonomiques')
                ->setHelp('Informations complémentaires sur l\'ergonomie de la salle')
                ->hideOnIndex(),
        ];
    }
}
