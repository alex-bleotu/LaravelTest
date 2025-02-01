<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'fatsecret_id',
        'name',
        'calories',
        'protein',
        'fat',
        'carbs',
        'fiber',
    ];
}
