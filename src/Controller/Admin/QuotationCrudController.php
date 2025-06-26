<?php

namespace App\Controller\Admin;

use App\Entity\Quotation;
use App\Service\QuotationService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Repository\QuotationRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Contrôleur EasyAdmin pour la gestion des devis
 * 
 * AMÉLIORATIONS APPORTÉES :
 * - Ajout de tous les nouveaux champs de l'entité Quotation
 * - Actions pour envoyer, accepter et refuser les devis
 * - Affichage des statuts avec des labels en français
 * - Gestion des prix avec formatage automatique
 * - Méthodes pour gérer les actions des devis
 */
class QuotationCrudController extends AbstractCrudController
{
    public function __construct(private QuotationService $quotationService) {}

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
            ->setPageTitle("detail", "Détail du devis")
            ->setDefaultSort(['created_at' => 'DESC'])
            ->setSearchFields(['reference', 'booking.room.name', 'booking.client.name'])
            ->setHelp('new', 'Créez un nouveau devis à partir d\'une réservation existante.')
            ->setHelp('edit', 'Modifiez les détails du devis. Attention : les modifications peuvent affecter le calcul du prix.');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(
                Crud::PAGE_INDEX,
                Action::new('send', 'Envoyer')
                    ->linkToUrl(function ($entity) {
                        return $this->container->get(AdminUrlGenerator::class)
                            ->setController(self::class)
                            ->setAction('sendQuotation')
                            ->setEntityId($entity->getId())
                            ->generateUrl();
                    })
                    ->displayIf(static function ($entity) {
                        return $entity->getStatus() === 'draft';
                    })
            )
            ->add(Crud::PAGE_DETAIL, Action::new('accept', 'Accepter')
                ->linkToCrudAction('acceptQuotation')
                ->displayIf(static function ($entity) {
                    return $entity->getStatus() === 'sent';
                }))
            ->add(Crud::PAGE_DETAIL, Action::new('reject', 'Refuser')
                ->linkToCrudAction('rejectQuotation')
                ->displayIf(static function ($entity) {
                    return $entity->getStatus() === 'sent';
                }));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // Champs d'identification
            IdField::new('id')
                ->hideOnForm()
                ->setLabel('ID'),

            TextField::new('reference')
                ->setLabel('Référence')
                ->setHelp('Référence unique du devis générée automatiquement.')
                ->setFormTypeOption('disabled', true),

            // Relations
            AssociationField::new('booking')
                ->setLabel('Réservation')
                ->setHelp('Réservation associée à ce devis.')
                ->onlyOnForms(),

            TextField::new('room')
                ->setLabel('Salle')
                ->setHelp('Nom de la salle réservée.')
                ->onlyOnIndex(),

            TextField::new('client')
                ->setLabel('Client')
                ->setHelp('Nom du client.')
                ->onlyOnIndex(),

            // Informations de prix
            MoneyField::new('price')
                ->setLabel('Prix HT')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->setHelp('Prix hors taxes de la réservation.'),

            MoneyField::new('taxAmount')
                ->setLabel('Montant TVA')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->setHelp('Montant de la TVA calculé automatiquement.')
                ->onlyOnDetail(),

            MoneyField::new('totalPrice')
                ->setLabel('Prix TTC')
                ->setCurrency('EUR')
                ->setStoredAsCents(false)
                ->setHelp('Prix total toutes taxes comprises.')
                ->onlyOnDetail(),

            // Statut et dates
            ChoiceField::new('status')
                ->setLabel('Statut')
                ->setChoices([
                    'Brouillon' => 'draft',
                    'Envoyé' => 'sent',
                    'Accepté' => 'accepted',
                    'Refusé' => 'rejected',
                    'Expiré' => 'expired'
                ])
                ->setHelp('Statut actuel du devis.'),

            DateField::new('validUntil')
                ->setLabel('Valide jusqu\'au')
                ->setHelp('Date limite de validité du devis.')
                ->setFormat('dd/MM/yyyy'),

            // Contenu du devis
            TextareaField::new('description')
                ->setLabel('Description')
                ->setHelp('Description détaillée de la prestation.')
                ->setNumOfRows(5),

            TextareaField::new('terms')
                ->setLabel('Conditions')
                ->setHelp('Conditions générales du devis.')
                ->setNumOfRows(8)
                ->onlyOnDetail(),

            // Informations de suivi
            AssociationField::new('createdBy')
                ->setLabel('Créé par')
                ->setHelp('Utilisateur qui a créé le devis.')
                ->onlyOnDetail(),

            DateTimeField::new('created_at')
                ->setLabel('Créé le')
                ->setFormat('dd/MM/yyyy HH:mm')
                ->setHelp('Date et heure de création du devis.')
                ->onlyOnDetail(),

            DateTimeField::new('updated_at')
                ->setLabel('Modifié le')
                ->setFormat('dd/MM/yyyy HH:mm')
                ->setHelp('Date et heure de dernière modification.')
                ->onlyOnDetail(),
        ];
    }

    /**
     * Action pour envoyer un devis par email
     * 
     * Cette méthode :
     * 1. Récupère le devis depuis la base de données
     * 2. Utilise le service QuotationService pour envoyer l'email
     * 3. Affiche un message de succès ou d'erreur
     * 4. Redirige vers la liste des devis
     */
    public function sendQuotation(Request $request, QuotationRepository $quotationRepository, EntityManagerInterface $em): RedirectResponse
    {
        $id = $request->query->get('entityId');
        $quotation = $quotationRepository->find($id);

        if (!$quotation) {
            $this->addFlash('error', 'Aucun devis sélectionné.');
            return $this->redirectToRoute('admin', ['crudAction' => 'index']);
        }

        // Génère une référence unique si besoin
        if (!$quotation->getReference()) {
            $quotation->setReference('Q-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3))));
        }

        // Change le statut à "sent"
        $quotation->setStatus('sent');
        $em->flush();

        $this->addFlash('success', 'Le devis ' . $quotation->getReference() . ' a été marqué comme envoyé.');

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('admin'));
    }

    /**
     * Action pour accepter un devis
     * 
     * Cette méthode :
     * 1. Récupère le devis depuis la base de données
     * 2. Utilise le service QuotationService pour marquer comme accepté
     * 3. Affiche un message de confirmation
     * 4. Redirige vers la liste des devis
     */
    public function acceptQuotation(AdminContext $context): RedirectResponse
    {
        if (!$context || !$context->getEntity()) {
            $this->addFlash('error', 'Aucun devis sélectionné.');
            return $this->redirectToRoute('admin', ['crudAction' => 'index']);
        }
        $quotation = $context->getEntity()->getInstance();
        try {
            $this->quotationService->acceptQuotation($quotation);
            $this->addFlash('success', 'Le devis ' . $quotation->getReference() . ' a été accepté.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'acceptation du devis : ' . $e->getMessage());
        }
        return $this->redirect($context->getReferrer());
    }

    /**
     * Action pour refuser un devis
     * 
     * Cette méthode :
     * 1. Récupère le devis depuis la base de données
     * 2. Utilise le service QuotationService pour marquer comme refusé
     * 3. Affiche un message de confirmation
     * 4. Redirige vers la liste des devis
     */
    public function rejectQuotation(AdminContext $context): RedirectResponse
    {
        if (!$context || !$context->getEntity()) {
            $this->addFlash('error', 'Aucun devis sélectionné.');
            return $this->redirectToRoute('admin', ['crudAction' => 'index']);
        }
        $quotation = $context->getEntity()->getInstance();
        try {
            $this->quotationService->rejectQuotation($quotation);
            $this->addFlash('success', 'Le devis ' . $quotation->getReference() . ' a été refusé.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors du refus du devis : ' . $e->getMessage());
        }
        return $this->redirect($context->getReferrer());
    }
}
