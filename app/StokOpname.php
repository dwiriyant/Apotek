<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    protected $fillable = [
        'id_obat','stok_software','stok_aplikasi', 
    ];

    protected $table = 'stok_opname';

    public function obat()
    {
        return $this->hasOne('App\Obat','id','id_obat');
    }

}