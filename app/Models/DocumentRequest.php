<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class DocumentRequest extends Model
{
    use HasFactory, HasRoles;

    protected $casts = [];

    public function document(){
        return $this->belongsTo(Document::class);
    }

    public function requester(){
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignedTo(){
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approvals(){
        return $this->hasMany(Approval::class);
    }
}
