<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'unit'];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
