<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class KaryawanController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(): Response
    {
        $karyawans = Karyawan::orderBy('created_at', 'desc')->get();

        return Inertia::render('karyawans/Index', [
            'karyawans' => $karyawans,
        ]);
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_id' => 'required|string|unique:karyawans,staff_id',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
            'bank_account_name' => 'required|string|max:255',
            'npwp' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        Karyawan::create($validated);

        return redirect()->route('karyawans.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Karyawan $karyawan): RedirectResponse
    {
        $validated = $request->validate([
            'staff_id' => 'required|string|unique:karyawans,staff_id,' . $karyawan->id,
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
            'bank_account_name' => 'required|string|max:255',
            'npwp' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        $karyawan->update($validated);

        return redirect()->route('karyawans.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Karyawan $karyawan): RedirectResponse
    {
        $karyawan->delete();

        return redirect()->route('karyawans.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
