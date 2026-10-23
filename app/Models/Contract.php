<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'subject',
        'activation_date',
        'approved_at',
        'created_by',
        'approved_by',
        'status',
        'rejection_reason',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'activation_date' => 'date',
        'approved_at' => 'datetime',
        'created_by' => 'integer',
        'approved_by' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents()
    {
        return $this->hasMany(ContractDocument::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function statusLabel()
    {
        $labels = [
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
        ];

        return $labels[$this->status];
    }

    public function documentChip()
    {
        $count = $this->documents->count();

        if ($count === 0) {
            return '—';
        }

        if ($count > 1) {
            return $count.' file';
        }

        $mime = (string) $this->documents->first()->mime;

        if (strpos($mime, 'image/') === 0) {
            return 'Image';
        }

        return 'PDF';
    }
}
