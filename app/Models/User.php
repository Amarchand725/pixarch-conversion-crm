<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Lab404\Impersonate\Models\Impersonate;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Traits\LogsModelActivity;
use App\Modules\Lead\Models\Lead;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable, ModelTrait, HasFactory, HasRoles, SoftDeletes, LogsModelActivity, Impersonate;

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
        'doj',
        'phone',
        'daily_capacity',
        'weight',
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

    public function canImpersonate(): bool
    {
        // Only admins can impersonate others
        return $this->hasRole('Admin');
    }

    public function canBeImpersonated(): bool
    {
        // Prevent impersonating admins and yourself
        return !$this->hasRole('Admin') && $this->id !== auth()->id();
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

    public function statusInfo()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function leads()
    {
        // Many-to-many via pivot table `entity_relations`
        return $this->morphedByMany(
            Lead::class,         // Related model
            'model',             // Morph name in pivot (model_type/model_id)
            'entity_relationships', // Pivot table name
            'user_id',           // Foreign key on pivot pointing to user
            'model_id'           // Foreign key on pivot pointing to lead
        )->withTimestamps();
    }

    // Lead activities for this user
    public function leadLogs()
    {
        return $this->hasMany(LogEntityStatus::class, 'author_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function meetings()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_users');
    }
}
