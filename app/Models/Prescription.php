<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $table = 'prescriptions';
    protected $primaryKey = 'prescription_id';
    protected $fillable = [
        'prescription_date',
        'card_id',
    ];

    public function Card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'card_id');
    }

    public function prescriptionMedications()
    {
        return $this->hasMany(PrescriptionMedication::class, 'prescription_id', 'prescription_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'prescription_id', 'prescription_id');
    }


}
