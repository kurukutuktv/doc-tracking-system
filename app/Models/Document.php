<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    HasMany
};

class Document extends Model
{
    protected $fillable = [
        'direction',
        'document_type_id',
        'priority_level',
        'control_number',
        'registry_date',
        'received_date',
        'sent_date',
        'title',
        'subject',
        'description',
        'file_path',
        'sender_id',
        'sender_name',
        'recipient_id',
        'current_office_id',
        'status_id',
        'parent_document_id',
        'transmission_mode_id',
        'created_by',
    ];

    /* ===================== RELATIONSHIPS ===================== */

    public function type(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(DocumentStatus::class);
    }

    public function transmissionMode(): BelongsTo
    {
        return $this->belongsTo(TransmissionMode::class);
    }

    public function currentOffice(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'current_office_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'parent_document_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Document::class, 'parent_document_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(DocumentLog::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments()
    {
        return $this->hasMany(DocumentAttachment::class);
    }
}
