<?php

namespace App\Jobs;

use App\Models\Slip;
use App\Http\Services\PiwapiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SendSlipWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $slip;
    protected $pdfUrl;

    /**
     * Create a new job instance.
     */
    public function __construct(Slip $slip, string $pdfUrl)
    {
        $this->slip = $slip;
        $this->pdfUrl = $pdfUrl;
    }

    public function handle(): void
    {
        $this->slip->loadMissing('karyawan');

        if (empty($this->slip->karyawan) || empty($this->slip->karyawan->phone)) {
            Log::warning("Slip ID {$this->slip->id} has no karyawan phone record, skipping WhatsApp dispatch.");
            return;
        }

        try {
            // Format period nicely
            $periodFormatted = '';
            try {
                $start = new \DateTime($this->slip->periode_start . '-01');
                $periodFormatted = $start->format('F Y');
            } catch (\Exception $e) {
                $periodFormatted = $this->slip->periode_start;
            }

            // Message content
            $messageTemplate = "Halo {{name}},\n\nBerikut adalah Slip Gaji Anda untuk periode *{{period}}* dari *PT SINAR ARTA DIGITAL*.\n\nTotal Gaji Diterima: *Rp {{total}}*\nStatus Pembayaran: *PAID*\n\nSilakan unduh dokumen PDF terlampir untuk rincian selengkapnya.\n\nTerima Kasih.";

            // Initialize PiwapiService and send document message
            $piwapi = new PiwapiService();
            $response = $piwapi->data([
                'name' => $this->slip->karyawan->name,
                'period' => $periodFormatted,
                'total' => number_format($this->slip->total_salary, 0, ',', '.'),
            ])
            ->setRecipient($this->slip->karyawan->phone)
            ->message($messageTemplate)
            ->document($this->pdfUrl, 'pdf')
            ->send();

            Log::info("WhatsApp Slip Sent to Karyawan phone {$this->slip->karyawan->phone} (Slip ID: {$this->slip->id}). Piwapi Response: " . json_encode($response) . " | URL: {$this->pdfUrl}");

        } catch (\Exception $e) {
            Log::error("Failed to process WhatsApp Slip Delivery for Slip ID {$this->slip->id}. Error: " . $e->getMessage());
            throw $e; // Re-throw so job status is marked as failed on queue
        }
    }
}
