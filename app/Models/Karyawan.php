<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'name',
        'position',
        'salary',
        'bank_name',
        'bank_account',
        'bank_account_name',
        'npwp',
        'phone',
    ];

    /**
     * Get the slips for the employee.
     */
    public function slips(): HasMany
    {
        return $this->hasMany(Slip::class);
    }
}
