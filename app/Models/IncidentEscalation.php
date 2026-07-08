<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentEscalation extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'escalated_to',
        'reason',
        'escalated_by',
        'escalated_at',
        'acknowledged_at',
        'notes',
    ];

    protected $casts = [
        'escalated_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public $timestamps = false;

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function escalatedTo()
    {
        return $this->belongsTo(Staff::class, 'escalated_to');
    }

    public function escalatedBy()
    {
        return $this->belongsTo(AdminUser::class, 'escalated_by');
    }

    public function acknowledge(): void
    {
        $this->update(['acknowledged_at' => now()]);
    }
}
