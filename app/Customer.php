<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'nama','alamat', 'telepon', 'jk', 'tgl_lahir','pekerjaan','email', 
    ];

    protected $table = 'customer';

}