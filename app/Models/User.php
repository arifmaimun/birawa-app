<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    protected static function booted(): void
    {
        static::updating(function (User $user) {
            if ($user->isDirty('avatar')) {
                Log::info("User avatar updated for user {$user->id}", [
                    'old_avatar' => $user->getOriginal('avatar'),
                    'new_avatar' => $user->avatar,
                    'timestamp' => now(),
                ]);
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'avatar',
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
            'password' => 'hashed',
        ];
    }

    public function doctorProfile()
    {
        return $this->hasOne(DoctorProfile::class);
    }

    public function serviceCatalogs()
    {
        return $this->hasMany(DoctorServiceCatalog::class);
    }

    public function inventories()
    {
        return $this->hasMany(DoctorInventory::class);
    }

    // Friendship Relationships
    public function friendships()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    public function friendRequestsReceived()
    {
        return $this->hasMany(Friendship::class, 'friend_id')->where('status', 'pending');
    }

    // Helper to get accepted friends
    public function getFriendsAttribute()
    {
        $friendsOfMine = $this->friendships()->where('status', 'accepted')->get()->pluck('friend');
        $friendOf = $this->hasMany(Friendship::class, 'friend_id')->where('status', 'accepted')->get()->pluck('user');

        return $friendsOfMine->merge($friendOf);
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return Storage::url($this->avatar) . '?t=' . $this->updated_at->timestamp;
        }
        return null;
    }

    // Chat Relationships
    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function messagesReceived()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function pets()
    {
        return $this->belongsToMany(Patient::class, 'pet_owners');
    }
}
