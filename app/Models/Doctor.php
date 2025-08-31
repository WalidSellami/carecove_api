<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctors';
    protected $primaryKey = 'doctor_id';
    protected $fillable = [
        'local_address',
        'specialty',
        'certificat_image',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'doctor_id', 'doctor_id');
    }

    public function patientClaims()
    {
        return $this->hasMany(PatientClaim::class, 'doctor_id', 'doctor_id');
    }



}
