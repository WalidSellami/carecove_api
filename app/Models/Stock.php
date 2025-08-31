<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';
    protected $primaryKey = 'stock_id';
    protected $fillable = [
        'quantity',
        'date_of_manufacture',
        'date_of_expiration',
        'medication_image',
        'medication_id',
        'pharmacy_id',
    ];

    public function medication()
    {
        return $this->belongsTo(Medication::class, 'medication_id', 'medication_id');
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class, 'pharmacy_id', 'pharmacy_id');
    }
}
