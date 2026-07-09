<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSwitch extends Model
{
    protected $guarded = [];

    public function panel()
    {
        return $this->belongsTo(Panel::class);
    }}
