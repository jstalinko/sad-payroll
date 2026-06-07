<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('send:whatsapp-test', function () {
    $this->info("Scanning for slip PDF files...");
    
    $slipsPath = storage_path('app/public/slips');
    $pdfFiles = glob($slipsPath . '/*.pdf');
    
    if (empty($pdfFiles)) {
        $this->error("No PDF files found in {$slipsPath}!");
        return;
    }
    
    $selectedPdf = $pdfFiles[0];
    $pdfFilename = basename($selectedPdf);
    $pdfUrl = url('/storage/slips/' . $pdfFilename);
    
    $this->info("Selected PDF: {$pdfFilename}");
    $this->info("PDF URL: {$pdfUrl}");
    
    $messageTemplate = "Halo {{name}},\n\nBerikut adalah Slip Gaji Anda untuk periode *{{period}}* dari *PT SINAR ARTA DIGITAL*.\n\nTotal Gaji Diterima: *Rp {{total}}*\nStatus Pembayaran: *PAID*\n\nSilakan unduh dokumen PDF terlampir untuk rincian selengkapnya.\n\nTerima Kasih.";

    $piwapi = new \App\Http\Services\PiwapiService();
    $recipient = '6287857580910';
    
    $this->info("Sending message to {$recipient} via Piwapi...");
    
    try {
        $response = $piwapi->data([
            'name' => 'Budi Santoso (Test Command)',
            'period' => 'June 2026',
            'total' => '8.500.000',
        ])
        ->setRecipient($recipient)
        ->message($messageTemplate)
        ->document($pdfUrl, 'pdf')
        ->send();
        
        $this->info("Response received: " . json_encode($response));
    } catch (\Exception $e) {
        $this->error("Error occurred: " . $e->getMessage());
    }
})->purpose('Send a test WhatsApp salary slip');
