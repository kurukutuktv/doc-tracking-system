<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'audience_users' => 'array',
        'next_approver_ids' => 'array',
        'acknowledged_by' => 'array',
        'meta' => 'array',
    ];

    // relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currentApprover()
    {
        return $this->belongsTo(User::class, 'current_approver_id');
    }

    public function logs()
    {
        return $this->hasMany(DocumentLog::class);
    }
}
