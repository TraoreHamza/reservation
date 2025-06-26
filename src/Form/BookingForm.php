<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\Booking;
use App\Entity\Equipment;
use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class BookingForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'name',
                'required' => true,
                'constraints' => [
                    new NotNull(message: 'Veuillez sélectionner une salle.'),
                ],
                'label' => 'Salle'
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner une date de début.'),
                ],
                'label' => 'Date de début'
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner une date de fin.'),
                ],
                'label' => 'Date de fin'
            ])
            ->add('equipments', EntityType::class, [
                'class' => Equipment::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('options', EntityType::class, [
                'class' => Option::class,
                'choice_label' => function ($option) {
                    return $option->getName() . ' (+' . number_format($option->getPrice(), 2) . '€)';
                },
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Réserver',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
