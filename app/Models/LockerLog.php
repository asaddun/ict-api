<?php

namespace App\Models;

use App\Models\Locker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LockerLog extends Model
{
    use HasFactory;
    protected $connection = 'hris';
    protected $table = 'lkr_log';
    protected $primaryKey = 'lkr_log_id';

    protected $fillable = [
        'lkr_locker_id',
        'c_employee_id',
        'action_type',
        'timestamp',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'c_employee_id', 'c_employee_id');
    }

    public function locker()
    {
        return $this->belongsTo(Locker::class, 'lkr_locker_id');
    }
}
