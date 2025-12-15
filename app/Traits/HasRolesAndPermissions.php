<?php
namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesAndPermissions
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role')
            ->withTimestamps();
    }

    public function hasRole(string $roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->roles()->whereIn('slug', $roleSlugs)->exists();
    }

    public function hasPermission(string $permissionSlug): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permissionSlug)) {
                return true;
            }
        }
        return false;
    }

    public function hasAnyPermission(array $permissionSlugs): bool
    {
        foreach ($permissionSlugs as $permissionSlug) {
            if ($this->hasPermission($permissionSlug)) {
                return true;
            }
        }
        return false;
    }

    public function getAllPermissions(): array
    {
        $permissions = [];
        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[$permission->slug] = $permission;
            }
        }
        return array_values($permissions);
    }

    public function assignRole(Role $role): void
    {
        if (!$this->hasRole($role->slug)) {
            $this->roles()->attach($role);
        }
    }

    public function removeRole(Role $role): void
    {
        $this->roles()->detach($role);
    }
}
