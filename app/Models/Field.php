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

    protected $appends = ['status'];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function histories()
{
    return $this->hasMany(FieldHistory::class)->orderBy('created_at', 'desc');
}


// app/Models/Field.php

public function getStatusAttribute(): string
{
    // 1. Terminal State
    if ($this->current_stage === 'harvested') {
        return 'Completed';
    }

    // 2. Threshold Check (14 days)
    // We check updated_at. Since we have an Observer, 
    // updated_at is always refreshed when stage or notes change.
    $threshold = now()->subDays(14);
    
    if ($this->updated_at < $threshold) {
        return 'At Risk';
    }

    return 'Active';
}

}