<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaksiPembelian extends Model
{
    protected $fillable = [
        'id_pembelian','kode_obat', 'jumlah','total','total_harga',
    ];

    protected $table = 'transaksi_pembelian';

    public function obat_po()
    {
        return $this->hasOne('App\ObatPO','kode','kode_obat');
    }

    public function obat()
    {
        return $this->hasOne('App\Obat','kode','kode_obat');
    }

    public function pembelian()
    {
        return $this->belongsTo('App\Pembelian','id_pembelian','id');
    }

}