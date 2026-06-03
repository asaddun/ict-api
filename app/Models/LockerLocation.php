<?php

namespace App\Models;

use App\Models\Locker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LockerLocation extends Model
{
    use HasFactory;
    protected $connection = 'hris';
    protected $table = 'lkr_location';
    protected $primaryKey = 'lkr_location_id';

    public function lokers()
    {
        return $this->hasMany(Locker::class, 'lkr_location_id');
    }
}
