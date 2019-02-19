<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $fillable = [
        'kode','nama','kategori', 'tgl_kadaluarsa', 'harga_jual_satuan', 'harga_jual_resep','harga_jual_grosir','stok', 
    ];

    protected $table = 'obat';

}