<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = [
        'id_obat','id_dokter','id_konsumen','type', 'jumlah', 'diskon', 'total','tanggal','status', 
    ];

    protected $table = 'penjualan';

    public function customer()
    {
        return $this->hasOne('App\Customer','id','id_konsumen');
    }

    public function dokter()
    {
        return $this->hasOne('App\Dokter','id','id_dokter');
    }
}   