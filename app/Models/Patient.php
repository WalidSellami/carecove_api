<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';
    protected $primaryKey = 'patient_id';
    protected $fillable = [
        'address',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'patient_id', 'patient_id');
    }

    public function patientClaims()
    {
        return $this->hasMany(PatientClaim::class, 'patient_id', 'patient_id');
    }


}
