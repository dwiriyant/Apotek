<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    protected $fillable = [
        'nama','alamat', 'telepon', 'jk', 'tgl_lahir','jenis','email', 
    ];

    protected $table = 'dokter';

}