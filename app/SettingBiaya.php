<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SettingBiaya extends Model
{
    protected $fillable = [
        'nama','deskripsi','periode','status'
    ];

    protected $table = 'setting_biaya';

}