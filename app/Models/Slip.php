<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slip extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'periode_start',
        'periode_end',
        'total_salary',
        'total_deduction',
        'basic_salary',
        'overtime_salary',
        'meal_allowance',
        'transportation_allowance',
        'bonus_salary',
        'bonus_notes',
        'late_deduction',
        'absence_deduction',
        'damaged_cost',
        'other_deduction',
        'other_deduction_notes',
        'status',
        'paid_at',
        'paid_by',
    ];

    protected $casts = [
        'total_salary' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'basic_salary' => 'decimal:2',
        'overtime_salary' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'transportation_allowance' => 'decimal:2',
        'bonus_salary' => 'decimal:2',
        'late_deduction' => 'decimal:2',
        'absence_deduction' => 'decimal:2',
        'damaged_cost' => 'decimal:2',
        'other_deduction' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Boot the model and add saving listener.
     */
    protected static function booted(): void
    {
        static::saving(function (Slip $slip) {
            // Calculate total deduction
            $slip->total_deduction = 
                (float)($slip->late_deduction ?? 0) +
                (float)($slip->absence_deduction ?? 0) +
                (float)($slip->damaged_cost ?? 0) +
                (float)($slip->other_deduction ?? 0);

            // Calculate total salary
            $slip->total_salary = 
                (float)($slip->basic_salary ?? 0) +
                (float)($slip->overtime_salary ?? 0) +
                (float)($slip->meal_allowance ?? 0) +
                (float)($slip->transportation_allowance ?? 0) +
                (float)($slip->bonus_salary ?? 0) -
                (float)($slip->total_deduction);
        });
    }

    /**
     * Get the employee that owns the slip.
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}
