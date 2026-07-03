<?php

namespace App\Jobs;

use App\Http\Services\Utils\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsappReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $phone;

    public function __construct(string $phone)
    {
        $this->phone = $phone;
    }

    public function handle()
    {
        $whatsapp = new WhatsAppService();

        $whatsapp->sendTemplate(
            $this->phone,
            "reminder_change",
            [],
            'es_CO'
        );
    }
}
