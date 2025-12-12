<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Approval extends Model
{
    use HasFactory, HasRoles;

    public function request(){
        return $this->belongsTo(DocumentRequest::class, 'document_request_id');
    }

    public function approver(){
        return $this->belongsTo(User::class, 'approver_id');
    }
}
