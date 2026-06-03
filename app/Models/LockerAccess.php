<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockerAccess extends Model
{
    use HasFactory;
    protected $connection = 'hris';
    protected $table = 'lkr_access';
    protected $primaryKey = 'lkr_access_id';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    protected $fillable = [
        'c_employee_id',
        'lkr_location_id',
        'createdby',
        'updatedby',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->createdby = 1497; // c_employee_id: Muhammad Asad
            $model->updatedby = 1497;
        });

        static::updating(function ($model) {
            $model->updatedby = 1497;
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'c_employee_id', 'c_employee_id');
    }

    public function location()
    {
        return $this->belongsTo(LockerLocation::class, 'lkr_location_id', 'lkr_location_id');
    }
}
