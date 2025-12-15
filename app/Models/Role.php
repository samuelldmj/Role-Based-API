<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];


    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role')
            ->withTimestamps();
    }


    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }


    public function givePermission(Permission $permission): void
    {
        if (!$this->hasPermission($permission->slug)) {
            $this->permissions()->attach($permission);
        }
    }


    public function revokePermission(Permission $permission): void
    {
        $this->permissions()->detach($permission);
    }
}
