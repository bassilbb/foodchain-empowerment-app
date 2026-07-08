<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_number',
        'user_id',
        'branch_id',
        'department_id',
        'assigned_to',
        'title',
        'description',
        'priority',
        'status',
        'category',
        'location',
        'reported_by',
        'reported_at',
        'resolved_at',
        'closed_at',
        'resolution_notes',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($incident) {
            if (empty($incident->incident_number)) {
                $incident->incident_number = self::generateIncidentNumber();
            }
            if (empty($incident->reported_at)) {
                $incident->reported_at = now();
            }
        });
    }

    public static function generateIncidentNumber(): string
    {
        do {
            $number = 'INC-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6));
        } while (self::where('incident_number', $number)->exists());

        return $number;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(AdminUser::class, 'assigned_to');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function escalations()
    {
        return $this->hasMany(IncidentEscalation::class);
    }

    public function comments()
    {
        return $this->hasMany(IncidentComment::class);
    }

    public function attachments()
    {
        return $this->hasMany(IncidentAttachment::class);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'escalated']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'in_progress', 'escalated']);
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function assignTo(AdminUser $admin): void
    {
        $this->update([
            'assigned_to' => $admin->id,
            'status' => 'in_progress',
        ]);
    }

    public function escalate(Staff $staff, string $reason, ?AdminUser $escalatedBy = null): IncidentEscalation
    {
        $this->update(['status' => 'escalated']);

        return $this->escalations()->create([
            'escalated_to' => $staff->id,
            'reason' => $reason,
            'escalated_by' => $escalatedBy?->id,
            'escalated_at' => now(),
        ]);
    }

    public function resolve(string $notes): void
    {
        $this->update([
            'status' => 'resolved',
            'resolution_notes' => $notes,
            'resolved_at' => now(),
        ]);
    }

    public function close(): void
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'status' => 'open',
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    public function getPriorityColor(): string
    {
        return match ($this->priority) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
            default => 'secondary',
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'open' => 'info',
            'in_progress' => 'primary',
            'escalated' => 'warning',
            'pending' => 'secondary',
            'resolved' => 'success',
            'closed' => 'dark',
            default => 'secondary',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return $this->getPriorityColor();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->getStatusColor();
    }

    public static function getCategories(): array
    {
        return [
            'technical',
            'operational',
            'customer_service',
            'logistics',
            'safety',
            'other',
        ];
    }

    public static function getPriorities(): array
    {
        return ['low', 'medium', 'high', 'critical'];
    }

    public static function getStatuses(): array
    {
        return ['open', 'in_progress', 'escalated', 'pending', 'resolved', 'closed'];
    }
}
