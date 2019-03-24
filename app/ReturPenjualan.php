<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturPenjualan extends Model
{
    protected $table = 'retur_penjualan';

    public function transaksi()
    {
        return $this->hasOne('App\TransaksiPenjualan','id','id_transaksi')->with('obat')->with('penjualan');
    }
}   