<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];

    /* =====================
     |  RELATIONSHIPS
     ===================== */

    // Users belonging to this department
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Approval hierarchy for this department
    public function approvalHierarchy()
    {
        return $this->hasMany(ApprovalHierarchy::class)
            ->orderBy('level');
    }

    // Documents originating from this department
    public function documents()
    {
        return $this->hasMany(Document::class, 'target_department_id');
    }
}
