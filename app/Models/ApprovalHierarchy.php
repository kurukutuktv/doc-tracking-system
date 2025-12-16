<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalHierarchy extends Model
{
    protected $fillable = [
        'department_id',
        'level',
        'role_id'
    ];

    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
