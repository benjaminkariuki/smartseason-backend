<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import trait
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'crop_type',
        'planting_date',
        'current_stage',
        'agent_id',
        'notes',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function histories()
{
    return $this->hasMany(FieldHistory::class)->orderBy('created_at', 'desc');
}
}