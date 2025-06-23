<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BookingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Réservation')
            ->setEntityLabelInPlural('Réservation')
            ->setPageTitle("index", 'Gestion des réservations')
            ->setPageTitle("new", 'Ajouter une réservation')
            ->setPageTitle("edit", 'Modifier une réservation')
            ->setPageTitle("detail", "Détail de la réservation")
            ->setSearchFields(['status', 'startDate', 'endDate']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Réservation')
                ->setIcon('fa fa-calendar'),
            FormField::addFieldset('Dates de la réservation'),
            DateField::new('startDate')
                ->setLabel('Date de démarrage')
                ->setHelp('Date et heure de début de la réservation.'),
            DateField::new('endDate')
                ->setLabel('Date de fin')
                ->setHelp('Date et heure de fin de la réservation.'),

            FormField::addFieldset('Informations de la réservation'),
            ChoiceField::new('status')
                ->setLabel('Statut de la réservation')
                ->setChoices([
                    'Validé' => 'confirmed',
                    'En attente' => 'pending',
                    'Annulé' => 'cancelled',
                ])
                ->setHelp('Statut actuel de la réservation (par exemple, validé, en attente, annulé).'),
            CollectionField::new('equipments')
                ->setLabel('Équipement')
                ->hideOnIndex()
                ->renderExpanded()
                ->setHelp('Nom de l\'équipement associé à la réservation.'),
            CollectionField::new('options')
                ->setLabel('Option')
                ->hideOnIndex()
                ->renderExpanded()
                ->setHelp('Nom de l\'option associée à la réservation.'),

            FormField::addFieldset('Informations sur la salle et le client'),
            AssociationField::new('room')
                ->setLabel('Salle')
                ->renderAsNativeWidget()
                ->setHelp('Nom de la salle associée à la réservation.'),
            AssociationField::new('client')
                ->setLabel('Client')
                ->renderAsNativeWidget()
                ->setHelp('Nom du client ayant effectué la réservation.'),
        ];
    }
}
