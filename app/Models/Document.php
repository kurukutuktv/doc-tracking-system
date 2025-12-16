<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'tracking_number',
        'title',
        'description',
        'file_path',
        'created_by',
        'department_id',
        'is_memo',
        'is_for_approval',
        'status',
        'audience_type',
        'audience_users',
        'current_approver_id',
        'current_approval_level',
    ];

    protected $casts = [
        'audience_users' => 'array',
        'next_approver_ids' => 'array',
        'acknowledged_by' => 'array',
        'is_memo' => 'boolean',
        'is_for_approval' => 'boolean',
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

    public function department()
    {
        return $this->belongsTo(Department::class);
    }


    public function logs()
    {
        return $this->hasMany(DocumentLog::class);
    }
}
