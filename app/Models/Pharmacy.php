<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    use HasFactory;

    protected $table = 'pharmacies';
    protected $primaryKey = 'pharmacy_id';
    protected $fillable = [
        'pharmacy_name',
        'local_address',
    ];


    public function pharmacists()
    {
        return $this->hasMany(Pharmacist::class, 'pharmacy_id', 'pharmacy_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'pharmacy_id', 'pharmacy_id');
    }

    public function stocks()
    {
        return $this->hasOne(Stock::class, 'pharmacy_id', 'pharmacy_id');
    }





}
