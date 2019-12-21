<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $fillable = [
        'kode','nama','kategori', 'tgl_kadaluarsa', 'harga_jual_satuan', 'harga_jual_resep','harga_jual_pack','satuan','stok','type', 
    ];

    protected $table = 'obat';

    public function kategori()
    {
        return $this->hasOne('App\Kategori','id','kategori');
    }

}