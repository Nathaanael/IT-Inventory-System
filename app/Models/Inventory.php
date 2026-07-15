<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'nama_user',
        'computer_name',
        'id_karyawan',
        'username_ad',
        'nomor_asset_pc',
        'department_id',
        'ip_address',
        'password_remote',
        'notes',
        'created_by'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
