<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
        'risk_percent',
        'stop_loss',
        'pair',
        'risk_amount_usd',
        'pip_value',
        'position_size',
        'account_currency',
    ];

    protected $casts = [
        'balance' => 'float',
        'risk_percent' => 'float',
        'stop_loss' => 'float',
        'risk_amount_usd' => 'float',
        'pip_value' => 'float',
        'position_size' => 'float',
    ];
}
