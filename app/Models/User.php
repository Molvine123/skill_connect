<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'avatar',
        'status',
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

    // ─── Relationships ────────────────────────────────────────────────────────

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function institution()
    {
        return $this->hasOne(Institution::class);
    }

    public function organization()
    {
        return $this->hasOne(Organization::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // ─── Role Helpers ─────────────────────────────────────────────────────────

    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isInstitution(): bool
    {
        return $this->hasRole('institution');
    }

    public function isOrganization(): bool
    {
        return $this->hasRole('organization');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getDashboardRoute(): string
    {
        return match($this->role?->name) {
            'admin'        => 'admin.dashboard',
            'institution'  => 'institution.dashboard',
            'organization' => 'organization.dashboard',
            'student'      => 'student.dashboard',
            default        => 'home',
        };
    }

    public function getRoleDisplayName(): string
    {
        return $this->role?->display_name ?? 'User';
    }

    public function getAvatarUrl(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=6366f1&color=fff&size=128&bold=true";
    }
}
