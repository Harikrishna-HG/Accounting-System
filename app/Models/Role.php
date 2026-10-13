<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    private ?Collection $permissionSlugsCache = null;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getPermissionSlugs(): Collection
    {
        if ($this->permissionSlugsCache !== null) {
            return $this->permissionSlugsCache;
        }

        if ($this->relationLoaded('permissions')) {
            $this->permissionSlugsCache = $this->permissions->pluck('slug');
        } else {
            $this->permissionSlugsCache = $this->permissions()->pluck('slug');
        }

        return $this->permissionSlugsCache;
    }

    public function hasPermission($permissionSlug)
    {
        return $this->getPermissionSlugs()->contains($permissionSlug);
    }
}
