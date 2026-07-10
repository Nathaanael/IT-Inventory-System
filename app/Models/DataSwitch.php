<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSwitch extends Model
{
    protected $fillable = ['panel_id', 'merk', 'ip_address', 'notes'];

    public function panel()
    {
        return $this->belongsTo(Panel::class);
    }}
