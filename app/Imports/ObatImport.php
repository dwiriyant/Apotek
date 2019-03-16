<?php

namespace App\Imports;

use App\Obat;
use App\Kategori;
use App\Satuan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

HeadingRowFormatter::default('none');

class ObatImport implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            if($row['Kode'] == null)
            {
                break;
            }

            $kode = $this->getObatByKode($row['Kode'], (int) $row['Stok']);
            
            if ($kode)
            {
                Obat::create([
                    'kode' => $row['Kode'],
                    'nama' => $row['Nama'],
                    'kategori' => (int) $this->getKategoriByNama($row['Kategori']),
                    'tgl_kadaluarsa' => transformDate($row['Tanggal Kadaluarsa']),
                    'harga_jual_satuan' => (int) $row['Harga Jual Satuan'],
                    'harga_jual_resep'  => (int) $row['Harga Jual Resep'],
                    'harga_jual_pack'  => (int) $row['Harga Jual Pack'],
                    'type' => strtolower($row['Status']) == 'konsinyasi' ? 2 : 1,
                    'satuan' => strtolower($row['Satuan']),
                    'stok' => (int) $row['Stok']
                ]);
            }
        }
    }

    function getKategoriByNama($nama)
    {
        $get = Kategori::where('nama', $nama)->first();

        if ($get == null) {
            $get = Kategori::create([
                'nama' => $nama
            ]);
        }

        return $get->id;
    }

    function getObatByKode($kode, $stok)
    {
        $get = Obat::where('kode', $kode)->where('status','!=',9)->first();

        if ($get != null) {
            $get->stok += $stok;
            $get->save();
            return false;
        }

        return true;
    }
}
