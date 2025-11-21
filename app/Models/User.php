<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Traits\LogsModelActivity;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable, ModelTrait, HasFactory, HasRoles,SoftDeletes, LogsModelActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $guard_name = 'user';

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'avatar_id',
        'gender',
        'dob',
        'phone',
        'two_factor',
        'notification',
        'status_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->status_id)) {
                $user->status_id = Status::where('model', 'User')
                    ->where('name', 'active')
                    ->value('id');
            }
        });
    }

    public function otpTokens()
    {
        return $this->morphMany(OtpToken::class, 'model');
    }

    public function avatar()
    {
        return $this->belongsTo(Attachment::class, 'avatar_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
