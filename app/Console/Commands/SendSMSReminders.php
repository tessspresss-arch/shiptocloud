<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SMSService;

class SendSMSReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer les SMS de rappel des rendez-vous';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Envoi des SMS de rappel des rendez-vous...');

        $result = SMSService::processPending();

        $this->info("✓ {$result['successful']} SMS envoyé(s) avec succès");
        if ($result['failed'] > 0) {
            $this->warn("✗ {$result['failed']} SMS échoué(s)");
        }
        $this->info("Total: {$result['total']} SMS traité(s)");

        return Command::SUCCESS;
    }
}
