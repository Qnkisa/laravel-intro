<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        "email",
        "password",
        "full_name",
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
