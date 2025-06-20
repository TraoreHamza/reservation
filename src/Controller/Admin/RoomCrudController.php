<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
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
            TextField::new('name')->setHelp('Nom de la salle.'),
            TextEditorField::new('description'),
            ImageField::new('image')
                ->setBasePath('medias/images/')
                ->setUploadDir('public/medias/images')
                ->setUploadedFileNamePattern('[year]/[month]/[day]/[slug]-[contenthash].[extension]'),
            IntegerField::new('capacity')
                ->setHelp('Capacité maximale de la salle.'),
            BooleanField::new('isAvailable')
                ->setHelp('Indique si la salle est disponible pour réservation.'),
        ];
    }
}
