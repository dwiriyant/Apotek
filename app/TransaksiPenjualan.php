<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaksiPenjualan extends Model
{
    protected $fillable = [
        'id_penjualan','kode_obat', 'jumlah','total','total_harga',
    ];

    protected $table = 'transaksi_penjualan';

    public function obat()
    {
        return $this->hasOne('App\Obat','kode','kode_obat');
    }

    public function penjualan()
    {
        return $this->belongsTo('App\Penjualan','id_penjualan','id');
    }

}