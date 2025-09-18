<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'ldrUser';
    protected $fillable = [
        'email_control_no',
        'password',
        'office',
        'control_no',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function roles()
    {
        /* Many-to-Many 👌*/
        return $this->belongsToMany(Role::class, 'ldrRole_user')->withTimestamps();
    }

    public function assignRole($role): void
    {
        $roleId = Role::where('name', $role)->first();
        if ($roleId) {
            $this->roles()->attach($roleId->id);
        }
    }

    public function hasRole($role): bool
    {
        return (bool)$this->roles()->where('name', $role)->first();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

}
