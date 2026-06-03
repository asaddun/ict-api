<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Copier extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'usage_date',
        'bw_counter',
        'color_counter',
        'total_counter',
        'limit',
        'bw_daily',
        'color_daily',
        'total_daily',
    ];
    protected $casts = [
        'usage_date' => 'date',
    ];
}
