<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Lead extends Model
{
    use HasFactory, SoftDeletes;
       protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'status',
        'assigned_to',
        'notes',
    ];
        protected $casts = [
        'status' => \App\Enums\LeadStatus::class,
    ];

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class);
    }
}
