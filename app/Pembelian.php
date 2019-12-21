<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $fillable = [
        'id_obat','id_dokter','id_konsumen','type', 'jumlah', 'diskon', 'total','tanggal','status', 
    ];

    protected $table = 'pembelian';

    public function supplier()
    {
        return $this->hasOne('App\Supplier','id','id_supplier');
    }

    public function transaksiPo()
    {
        return $this->hasMany('App\TransaksiPembelian','id_pembelian','id')->with('obat_po');
    }

    public function transaksi()
    {
        return $this->hasMany('App\TransaksiPembelian','id_pembelian','id')->with('obat');
    }

}