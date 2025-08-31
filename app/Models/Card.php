<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $table = 'cards';
    protected $primaryKey = 'card_id';
    protected $fillable = [
        'age',
        'weight',
        'sickness',
        'patient_id',
        'doctor_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'doctor_id');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'card_id', 'card_id');
    }
}
