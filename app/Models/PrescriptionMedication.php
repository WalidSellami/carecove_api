<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionMedication extends Model
{
    use HasFactory;

    protected $table = 'prescription_medications';
    protected $primaryKey = 'prescription_medication_id';
    protected $fillable = [
        'dosage',
        'prescription_id',
        'medication_id'
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class, 'prescription_id', 'prescription_id');
    }

    public function medication()
    {
        return $this->belongsTo(Medication::class, 'medication_id', 'medication_id');
    }


}
