<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObatPO extends Model
{
    protected $fillable = [
        'kode','nama','kategori', 'tgl_kadaluarsa','satuan','stok','type', 
    ];

    protected $table = 'obat_po';

    public function kategori()
    {
        return $this->hasOne('App\Kategori','id','kategori');
    }

}