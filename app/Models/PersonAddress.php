<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\State;

class PersonAddress extends Model
{
    use HasFactory;
    protected $table="person_address";
}
