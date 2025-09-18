<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $table = 'ldrRole';
    protected $fillable = ['name', 'updated_at', 'created_at'];
    protected $hidden = ['pivot'];

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'updated_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function users()
    {
        /* Many-to-Many 👌*/
        return $this->belongsToMany(User::class, 'ldrRole_user')->withTimestamps();
    }

}
