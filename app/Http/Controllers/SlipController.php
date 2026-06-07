<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Slip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response as LaravelResponse;

class SlipController extends Controller
{
    /**
     * Display a listing of active (non-paid) slips.
     */
    public function index(): Response
    {
        $slips = Slip::with('karyawan')
            ->where('status', '!=', 'PAID')
            ->orderBy('created_at', 'desc')
            ->get();

        $karyawans = Karyawan::orderBy('name', 'asc')->get();

        return Inertia::render('slips/Index', [
            'slips' => $slips,
            'karyawans' => $karyawans,
        ]);
    }

    /**
     * Store a newly created slip.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'periode_start' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'periode_end' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'basic_salary' => 'required|numeric|min:0',
            'overtime_salary' => 'nullable|numeric|min:0',
            'meal_allowance' => 'nullable|numeric|min:0',
            'transportation_allowance' => 'nullable|numeric|min:0',
            'bonus_salary' => 'nullable|numeric|min:0',
            'bonus_notes' => 'nullable|string',
            'late_deduction' => 'nullable|numeric|min:0',
            'absence_deduction' => 'nullable|numeric|min:0',
            'damaged_cost' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'other_deduction_notes' => 'nullable|string',
            'status' => 'nullable|string|in:PAID,DRAFT,OVERDUE,UNPAID,PARTIALLY_PAID',
        ]);

        // Set default status to DRAFT if not provided
        $validated['status'] = $validated['status'] ?? 'DRAFT';

        Slip::create($validated);

        return redirect()->route('slips.index')
            ->with('success', 'Salary slip generated successfully as DRAFT.');
    }

    /**
     * Mark selected slips as PAID.
     */
    public function markAsPaid(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:slips,id',
        ]);

        $currentUser = Auth::user();
        $paidBy = $currentUser ? $currentUser->name : 'System';

        $slips = Slip::whereIn('id', $validated['ids'])->get();
        $isMultiple = $slips->count() > 1;

        foreach ($slips as $slip) {
            $slip->update([
                'status' => 'PAID',
                'paid_at' => now(),
                'paid_by' => $paidBy,
            ]);

            if ($isMultiple) {
                // If multiple paid, use background queue to send WhatsApp
                \App\Jobs\SendSlipWhatsappJob::dispatch($slip);
            } else {
                // For single paid, process synchronously/instantly
                \App\Jobs\SendSlipWhatsappJob::dispatchSync($slip);
            }
        }

        return redirect()->back()->with('success', 'Slips marked as PAID successfully.');
    }

    /**
     * Download a single individual slip as PDF.
     */
    public function downloadIndividualPdf(Slip $slip): LaravelResponse
    {
        $slip->loadMissing('karyawan');
        $pdf = Pdf::loadView('pdf.slip_individual', compact('slip'));
        
        return $pdf->download('slip-gaji-' . str_replace(' ', '-', strtolower($slip->karyawan->name)) . '-' . $slip->id . '.pdf');
    }

    /**
     * Display a listing of paid slips.
     */
    public function paidList(): Response
    {
        $slips = Slip::with('karyawan')
            ->where('status', 'PAID')
            ->orderBy('paid_at', 'desc')
            ->get();

        return Inertia::render('slips/PaidList', [
            'slips' => $slips,
        ]);
    }

    /**
     * Download DRAFT slips as PDF.
     */
    public function downloadDraftPdf(): LaravelResponse
    {
        $slips = Slip::with('karyawan')
            ->where('status', 'DRAFT')
            ->orderBy('created_at', 'desc')
            ->get();

        $title = 'Draft Salary Data Report';
        $pdf = Pdf::loadView('pdf.slips', compact('slips', 'title'));
        
        return $pdf->download('draft-salary-slips.pdf');
    }

    /**
     * Download PAID slips as PDF.
     */
    public function downloadPaidPdf(): LaravelResponse
    {
        $slips = Slip::with('karyawan')
            ->where('status', 'PAID')
            ->orderBy('paid_at', 'desc')
            ->get();

        $title = 'Paid Salary Data Report';
        $pdf = Pdf::loadView('pdf.slips', compact('slips', 'title'));
        
        return $pdf->download('paid-salary-slips.pdf');
    }

    /**
     * Remove the specified slip.
     */
    public function destroy(Slip $slip): RedirectResponse
    {
        $slip->delete();

        return redirect()->back()->with('success', 'Slip deleted successfully.');
    }
}
