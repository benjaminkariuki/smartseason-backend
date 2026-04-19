<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldHistory extends Model
{
    use HasFactory;

    // Define the fields that are allowed to be mass-assigned
    protected $fillable = [
        'field_id',
        'user_id',
        'field_changed',
        'old_value',
        'new_value',
    ];

    public const UPDATED_AT = null;

    // Optional: Add relationship back to user for cleaner access
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}