<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\LockerLog;
use App\Models\LockerLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Locker extends Model
{
    use HasFactory;
    protected $connection = 'hris';
    protected $table = 'lkr_locker';
    protected $primaryKey = 'lkr_locker_id';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    protected $fillable = ['isactive', 'lkr_location_id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'c_employee_id', 'c_employee_id');
    }

    public function location()
    {
        return $this->belongsTo(LockerLocation::class, 'lkr_location_id', 'lkr_location_id');
    }

    public function logs()
    {
        return $this->hasMany(LockerLog::class, 'lkr_log_id', 'lkr_log_id');
    }
}
