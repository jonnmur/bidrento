<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edge extends Model
{
    use HasFactory;

    protected $table = 'edge';

    protected $casts = [
        'id' => 'integer',
        'from_id' => 'integer',
        'to_id' => 'integer',
    ];

    public $timestamps = false;
}
