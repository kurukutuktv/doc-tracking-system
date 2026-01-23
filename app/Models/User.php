<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Authorizable, HasRoles;

    protected $fillable = [
        'name',
        'last_name',
        'first_name',
        'middle_name',
        'name_extension',
        'post_nominals',
        'email',
        'password',
        'department_id',
        'approval_level',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ======================
       ACCESSORS
    =======================*/

    public function getFullNameAttribute(): string
    {
        $name = "{$this->last_name}, {$this->first_name}";

        if ($this->middle_name) {
            $name .= " {$this->middle_name}";
        }

        if ($this->name_extension) {
            $name .= ", {$this->name_extension}";
        }

        if ($this->post_nominals) {
            $name .= " ({$this->post_nominals})";
        }

        return trim($name);
    }

    public function getSignatureAttribute(): string
    {
        $name = "{$this->last_name}, {$this->first_name}";

        if ($this->middle_name) {
            $name .= " {$this->middle_name}";
        }

        if ($this->name_extension) {
            $name .= ", {$this->name_extension}";
        }

        if ($this->post_nominals) {
            $name .= ", {$this->post_nominals}";
        }

        return strtoupper($name);
    }

    /* ======================
       MUTATORS
    =======================*/

    protected function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = strtoupper(trim($value));
    }

    protected function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucwords(strtolower(trim($value)));
    }

    protected function setMiddleNameAttribute($value)
    {
        if (!$value) {
            $this->attributes['middle_name'] = null;
            return;
        }

        $parts = preg_split('/\s+/', trim($value));
        $initials = collect($parts)
            ->map(fn ($p) => strtoupper(substr($p, 0, 1)) . '.')
            ->implode('');

        $this->attributes['middle_name'] = $initials;
    }

    /* ======================
       RELATIONSHIPS
    =======================*/

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
