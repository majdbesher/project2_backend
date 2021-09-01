<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class trip extends Model
{
    use HasFactory;

    protected $fillable = [
            'seats',
            'date',
            'hour',
            'starting_point',
            'distination_point',
            'state',
            'type',
        ];
}
