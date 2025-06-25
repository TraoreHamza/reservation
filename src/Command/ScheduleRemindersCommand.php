<?php

namespace App\Command;

use App\Service\ReminderSchedulerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:schedule-reminders',
    description: 'Planifie tous les rappels pour les réservations validées à venir',
)]
class ScheduleRemindersCommand extends Command
{
    public function __construct(
        private ReminderSchedulerService $reminderScheduler
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Planification des rappels de réservation');

        try {
            $scheduledCount = $this->reminderScheduler->scheduleAllReminders();

            if ($scheduledCount > 0) {
                $io->success("✅ {$scheduledCount} rappel(s) planifié(s) avec succès !");
            } else {
                $io->info("ℹ️ Aucun rappel à planifier pour le moment.");
            }
        } catch (\Exception $e) {
            $io->error('❌ Erreur lors de la planification des rappels : ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
