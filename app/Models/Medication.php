<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $table = 'medications';
    protected $primaryKey = 'medication_id';
    protected $fillable = [
        'name',
        'description',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'medication_id', 'medication_id');
    }

    public function prescriptionMedications()
    {
        return $this->hasMany(PrescriptionMedication::class, 'medication_id', 'medication_id');
    }

}
