<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturPembelian extends Model
{
    protected $table = 'retur_pembelian';

    public function transaksi()
    {
        return $this->hasOne('App\TransaksiPembelian','id','id_transaksi')->with('obat')->with('pembelian');
    }
}   