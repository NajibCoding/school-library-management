<?php

namespace App\Http\Traits;

trait HasStatus
{
    public function scopeActive($query){
        return $query->where($this->getTable().'.status', 1);
    }

    public function scopeActiveWithRoleCheck($query)
    {
        if (!auth()->user()->hasRole('SUPERADMIN')) {
            return $query->where($this->getTable() . ".status", 1);
        }
        return $query;
    }

    public function scopeWithoutDeleted($query)
    {
        return $query->where($this->getTable() . ".status", '!=', 2);
    }
}
