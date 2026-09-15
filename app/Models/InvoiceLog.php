<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InvoiceLog extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'invoice_id',
        'event',
        'message',
        'error_detail',
        'sent_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at'    => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
