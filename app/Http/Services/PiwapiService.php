<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Helper;
use Illuminate\Support\Facades\Log;

class PiwapiService
{
    private $secret;
    private $account;
    private $recipient;
    private $message;
    private $data;
    private $document;
    private $type;
    private $parameters;
    public function __construct()
    {
        $this->secret = config('piwapi.secret');
        $this->account = config('piwapi.account');
        $this->parameters = [];
    }
    public function setRecipient($recipient)
    {
        if (empty($recipient)) {
            $this->recipient = null;
            return $this;
        }

        // Hapus spasi, strip, atau karakter non-angka lainnya
        $recipient = preg_replace('/\D/', '', $recipient);

        // Format phone number
        $recipient = preg_replace('/^08/', '628', $recipient);

        // Pastikan jika depannya masih 0, diubah ke 62 (opsional tergantung input)
        if (str_starts_with($recipient, '0')) {
            $recipient = '62' . substr($recipient, 1);
        }

        $this->recipient = $recipient;

        return $this;
    }
    public function data($data = [])
    {
        $this->data = $data;
        return $this;
    }
    protected function interpolate(string $message, array $data): string
    {
        // replace {{key}} => value
        foreach ($data as $key => $value) {
            $message = str_replace('{{' . $key . '}}', (string) $value, $message);
        }
        return $message;
    }
    public function message($message)
    {
        $this->message = $this->interpolate($message, $this->data);
        return $this;
    }

    public function document($document, $type = 'pdf')
    {

        $this->type = 'document';
        $this->parameters = [
            'document_url' => $document,
            'document_name' => basename($document),
            'document_type' => $type,
        ];
        return $this;
    }


    public function send()
    {
        // Jika recipient kosong atau message belum di-generate, jangan kirim apa-apa
        if (empty($this->recipient) || empty($this->message)) {
            return [
                "success" => false,
                "message" => "Recipient or message is empty, skipping send."
            ];
        }

        // Local development helper: if the file URL points to localhost or 127.0.0.1, upload to tmpfiles.org
        if (isset($this->parameters['document_url'])) {
            $pdfUrl = $this->parameters['document_url'];
            if (env('TMPFILES_UPLOAD_PDF', false)) {
                Log::info("PiwapiService: Local URL detected in document: {$pdfUrl}. Uploading to tmpfiles.org for external access.");
                try {
                    $parsedUrl = parse_url($pdfUrl);
                    $path = isset($parsedUrl['path']) ? urldecode($parsedUrl['path']) : '';
                    
                    // Convert "/storage/slips/slip-X.pdf" to "slips/slip-X.pdf"
                    $storagePath = preg_replace('/^\/storage\//', '', $path);
                    
                    if (Storage::disk('public')->exists($storagePath)) {
                        $fileContents = Storage::disk('public')->get($storagePath);
                        $fileName = basename($storagePath);
                        
                        $uploadResponse = Http::attach(
                            'file',
                            $fileContents,
                            $fileName
                        )->post('https://tmpfiles.org/api/v1/upload');
                        
                        if ($uploadResponse->successful()) {
                            $tempUrl = $uploadResponse->json('data.url');
                            if ($tempUrl) {
                                $this->parameters['document_url'] = str_replace('https://tmpfiles.org/', 'https://tmpfiles.org/dl/', $tempUrl);
                                Log::info("PiwapiService: Successfully uploaded to tmpfiles.org. Public URL: " . $this->parameters['document_url']);
                            }
                        } else {
                            Log::error("PiwapiService: Failed to upload to tmpfiles.org. Status: " . $uploadResponse->status() . " Body: " . $uploadResponse->body());
                        }
                    } else {
                        Log::warning("PiwapiService: Could not locate file locally in public storage for path: {$storagePath}");
                    }
                } catch (\Exception $e) {
                    Log::error("PiwapiService: Exception uploading local document to tmpfiles.org: " . $e->getMessage());
                }
            }
        }

        $url = "https://piwapi.com/api/send/whatsapp";
        $postFields = [
            "secret"    => $this->secret,
            "account"   => $this->account,
            "recipient" => $this->recipient,
            "type"      => $this->type,
            "message"   => $this->message,
            ...$this->parameters,
        ];

        // Gunakan Laravel Http Client agar lebih clean dibanding cURL manual
        try {
            $response = Http::asForm()->post($url, $postFields);
            return $response->json();
        } catch (\Exception $e) {
            return ["success" => false, "error" => $e->getMessage()];
        }
    }
}
