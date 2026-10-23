<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'contract_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public static function record($user, $contract, $description)
    {
        $activity = new self();
        $activity->user_id = $user ? $user->id : null;
        $activity->contract_id = $contract ? $contract->id : null;
        $activity->description = $description;
        $activity->save();

        return $activity;
    }
}
