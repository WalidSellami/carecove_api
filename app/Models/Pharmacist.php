<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacist extends Model
{
    use HasFactory;

    protected $table = 'pharmacists';
    protected $primaryKey = 'pharmacist_id';
    protected $fillable = [
        'certificat_image',
        'user_id',
        'pharmacy_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class, 'pharmacy_id', 'pharmacy_id');
    }





}
