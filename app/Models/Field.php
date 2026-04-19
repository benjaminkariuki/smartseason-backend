<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'crop_type',
        'planting_date',
        'current_stage',
        'agent_id'
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}