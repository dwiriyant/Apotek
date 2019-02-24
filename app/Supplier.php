<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name','alamat','kota','telepon','no_rek','email', 
    ];

    protected $table = 'supplier';

}