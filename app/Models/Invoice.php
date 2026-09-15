<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'invoice_number',
        'service',
        'value',
        'notes',
        'invoice_date',
        'pdf_path',
        'status',
        'is_paid',
        'pending_amount',
        'whatsapp_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'value'            => 'decimal:2',
            'pending_amount'   => 'decimal:2',
            'is_paid'          => 'boolean',
            'invoice_date'     => 'date',
            'whatsapp_sent_at' => 'datetime',
            'deleted_at'       => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function logs()
    {
        return $this->hasMany(InvoiceLog::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeForUser($query, string $userId)
    {
        // En un sistema monousuario, devolvemos todo sin filtrar.
        // Si a futuro es multiusuario, se debe agregar user_id a la tabla.
        return $query;
    }

    public function scopeByStatus($query, ?string $status)
    {
        if (empty($status)) {
            return $query;
        }

        if ($status === 'paid') {
            return $query->where('is_paid', true);
        }

        if ($status === 'unpaid') {
            return $query->where('is_paid', false);
        }

        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->whereDate('invoice_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('invoice_date', '<=', $to);
        }

        return $query;
    }

    public function scopeWithSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhere('service', 'like', "%{$search}%")
              ->orWhereHas('client', function ($cq) use ($search) {
                  $cq->where('name', 'like', "%{$search}%")
                     ->orWhere('whatsapp', 'like', "%{$search}%");
              });
        });
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getFormattedValueAttribute(): string
    {
        return number_format((float) $this->value, 2, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'sent'    => 'Enviada',
            'error'   => 'Error',
            default   => 'Pendiente',
        };
    }
}
