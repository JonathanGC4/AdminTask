<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'user_id',
        'assigned_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Helpers de due date
    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && $this->status !== 'completed';
    }

    public function isDueSoon(): bool
    {
        return $this->due_date
            && $this->due_date->isFuture()
            && $this->due_date->diffInDays(now()) <= 2
            && $this->status !== 'completed';
    }
}
