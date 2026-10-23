<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractDocument extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'original_name',
        'path',
        'mime',
        'size',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function isImage()
    {
        return strpos((string) $this->mime, 'image/') === 0;
    }

    public function kindLabel()
    {
        return $this->isImage() ? 'Image' : 'PDF';
    }

    public function sizeLabel()
    {
        $bytes = (int) $this->size;

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1048576) {
            return number_format($bytes / 1024, 0, ',', '.').' KB';
        }

        return number_format($bytes / 1048576, 1, ',', '.').' MB';
    }
}
