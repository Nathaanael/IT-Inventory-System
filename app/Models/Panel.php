<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    protected $fillable = ['name', 'location'];

    public function dataSwitches()
    {
        return $this->hasMany(DataSwitch::class);
    }
}
