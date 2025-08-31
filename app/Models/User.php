<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'name',
        'role',
        'phone',
        'email',
        'password',
        'profile_image',
        'code_auth',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'user_id', 'user_id');
    }

    public function patient()
    {
        return $this->hasOne(Patient::class, 'user_id', 'user_id');
    }

    public function pharmacist()
    {
        return $this->hasOne(Pharmacist::class, 'user_id', 'user_id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id', 'user_id');
    }

    public function userClaims()
    {
        return $this->hasMany(UserClaim::class, 'user_id', 'user_id');
    }

}
