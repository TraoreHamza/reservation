<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPageTitle("index", 'Gestion des utilisateurs')
            ->setPageTitle("new", 'Ajouter un utilisateur')
            ->setPageTitle("edit", 'Modifier un utilisateur')
            ->setPageTitle("detail", "Détail de l'utilisateur")
            ->setSearchFields(['email', 'roles'])
            ->setDefaultSort(['created_at' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(
                Crud::PAGE_INDEX,
                Action::new('ban', 'Bannir')
                    ->linkToCrudAction('banUser')
                    ->displayIf(static function ($entity) {
                        return !$entity->isBanned();
                    })
                    ->addCssClass('btn btn-danger')
                    ->setIcon('fa fa-ban')
            )
            ->add(
                Crud::PAGE_INDEX,
                Action::new('unban', 'Débannir')
                    ->linkToCrudAction('unbanUser')
                    ->displayIf(static function ($entity) {
                        return $entity->isBanned();
                    })
                    ->addCssClass('btn btn-success')
                    ->setIcon('fa fa-check')
            )
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setLabel('Modifier')->setIcon('fa fa-edit');
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email')
                ->setLabel('Email')
                ->setHelp('Adresse email de l\'utilisateur.'),

            ArrayField::new('roles')
                ->setLabel('Rôles')
                ->setHelp('Rôles attribués à l\'utilisateur.')
                ->hideOnForm(),

            BooleanField::new('is_banned')
                ->setLabel('Banni')
                ->setHelp('Indique si l\'utilisateur est banni.')
                ->renderAsSwitch(false),

            BooleanField::new('is_active')
                ->setLabel('Actif')
                ->setHelp('Indique si le compte est actif.')
                ->renderAsSwitch(false),

            BooleanField::new('is_verified')
                ->setLabel('Vérifié')
                ->setHelp('Indique si l\'email a été vérifié.')
                ->renderAsSwitch(false),

            IntegerField::new('warning')
                ->setLabel('Avertissements')
                ->setHelp('Nombre d\'avertissements reçus.')
                ->hideOnForm(),

            DateTimeField::new('created_at')
                ->setLabel('Date de création')
                ->setFormat('dd/MM/Y HH:mm')
                ->hideOnForm(),

            DateTimeField::new('updated_at')
                ->setLabel('Dernière modification')
                ->setFormat('dd/MM/Y HH:mm')
                ->hideOnForm(),

            AssociationField::new('client')
                ->setLabel('Client associé')
                ->setHelp('Client lié à cet utilisateur.')
                ->hideOnForm(),
        ];
    }

    public function banUser(User $user): Response
    {
        $user->setIsBanned(true);
        $this->entityManager->flush();

        $this->addFlash('success', 'Utilisateur banni avec succès.');
        return $this->redirectToRoute('admin', ['crudAction' => 'index', 'crudId' => null]);
    }

    public function unbanUser(User $user): Response
    {
        $user->setIsBanned(false);
        $this->entityManager->flush();

        $this->addFlash('success', 'Utilisateur débanni avec succès.');
        return $this->redirectToRoute('admin', ['crudAction' => 'index', 'crudId' => null]);
    }
}
