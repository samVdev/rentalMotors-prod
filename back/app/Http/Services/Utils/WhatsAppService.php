<?php

namespace App\Http\Services\Utils;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected string $token;
    protected string $phoneId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token');
        $this->phoneId = config('services.whatsapp.phone_id');
        $this->baseUrl = "https://graph.facebook.com/v22.0/{$this->phoneId}/messages";
    }

    public function sendText(string $to, string $message)
    {
        $to = preg_replace('/\D/', '', $to);
        return Http::timeout(15)->withToken($this->token)
            ->post($this->baseUrl, [
                "messaging_product" => "whatsapp",
                "to" => $to,
                "type" => "text",
                "text" => [
                    "preview_url" => false,
                    "body" => $message
                ]
            ])
            ->json();
    }

    public function sendTemplate(string $to, string $templateName, array $params = [], string $language = 'es')
    {
        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => [
                    "code" => $language
                ]
            ]
        ];
    
        if (!empty($params)) {
            $payload['template']['components'] = [
                [
                    "type" => "body",
                    "parameters" => array_map(fn($value) => [
                        "type" => "text",
                        "text" => $value
                    ], $params)
                ]
            ];
        }

        return Http::withToken($this->token)
            ->post($this->baseUrl, $payload)
            ->json();
    }
    
}
