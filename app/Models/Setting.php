<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_phone',
        'company_email',
        'company_address',
        'logo_path',
        'currency',
        'whatsapp_message',
        'invoice_prefix',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
