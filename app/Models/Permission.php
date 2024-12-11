<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions')->withTimestamps(); // Enable pivot table timestamps
    }
}
