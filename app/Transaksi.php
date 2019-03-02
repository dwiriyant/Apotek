<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'id_penjualan','kode_obat', 'jumlah',
    ];

    protected $table = 'transaksi';

}