<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Financing;
use App\Jobs\SendWhatsappReminder;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SendWhatsappRemindersCommand extends Command
{
    protected $signature = 'whatsapp:send-reminders';
    protected $description = 'Enviar recordatorios por WhatsApp a clientes con financiamiento activo';

    public function handle()
    {
        $dateLimit = Carbon::now()->subDays(15);

        Financing::where('status', 'active')
            ->where(function($q) use ($dateLimit) {
                $q->whereNull('last_whatsapp_sent')
                  ->orWhere('last_whatsapp_sent', '<=', $dateLimit);
            })
            ->with('user.persona')
            ->chunk(100, function (Collection $financings) {

                $financings->groupBy('user_id')->each(function ($userFinancings) {

                    $user = $userFinancings->first()->user;

                    if (!$user || !$user->persona || !$user->persona->phone) {
                        return;
                    }

                    SendWhatsappReminder::dispatch($user->persona->phone);

                    $userFinancings->each(function ($financing) {
                        $financing->update(['last_whatsapp_sent' => now()]);
                    });
                });
            });

        $this->info('Recordatorios enviados correctamente.');
    }
}
