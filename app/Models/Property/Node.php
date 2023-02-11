<?php

namespace App\Models\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    protected $table = 'node';

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
    ];

    protected $hidden = [
        'pivot',
    ];

    public $timestamps = false;

    public function parents()
    {
        return $this->belongsToMany(Node::class, 'edge', 'from_id', 'to_id')->with('children');
    }

    public function children()
    {
        return $this->belongsToMany(Node::class, 'edge', 'to_id', 'from_id')->with('children');
    }
}
