<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadActivity extends Model
{
    use HasFactory;
    protected $fillable = [
        'lead_id',
        'user_id',
        'action',
        'notes',
    ];

    protected $casts = [
        'action' => \App\Enums\LeadActivityAction::class,
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
