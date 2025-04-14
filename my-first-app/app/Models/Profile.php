<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Profile extends Model implements AuthenticatableContract
{
    use HasFactory, Authenticatable;

    protected $fillable = [
        "email",
        "password",
        "fullName",
        "address",
        "phone"
    ];

    public function setPasswordAttribute($pass) {
        $this -> attributes["password"] = Hash::make($pass);
    }
    protected $hidden = [
        'password'
    ];
}
