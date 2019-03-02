<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = [
        'id_obat','id_konsumen','type', 'jumlah', 'diskon', 'total','tanggal','status', 
    ];

    protected $table = 'penjualan';

}