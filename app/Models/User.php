<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'created_by');
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function roleLabel()
    {
        return $this->isSupervisor() ? 'Supervisor' : 'Staff';
    }

    public function initials()
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $letters = '';

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $letters .= mb_substr($part, 0, 1);
        }

        return mb_strtoupper(mb_substr($letters, 0, 2));
    }
}
