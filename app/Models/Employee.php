<?php

namespace App\Models;

use App\Models\Locker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;
    protected $connection = 'hris';
    protected $table = 'c_employee';
    protected $primaryKey = 'c_employee_id';

    // public function locker()
    // {
    //     return $this->hasMany(Locker::class, 'lkr_locker_id');
    // }
}
