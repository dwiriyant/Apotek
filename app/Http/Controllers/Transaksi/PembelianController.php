<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Obat;
use App\Pembelian;
use App\Kategori;
use App\Supplier;
use App\TransaksiPembelian;
use Auth;

class PembelianController extends Controller
{
    private $_route  = 'pembelian-reguler';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $last_id = (string)Pembelian::max('id');
        $len = strlen($last_id);
        
        if($len==3)
            $last_id = substr($last_id,$len-3,3);
        else if($len==2)
            $last_id = '0'.$last_id;
        else if($len==1)
            $last_id = '00'.$last_id;
        else
            $last_id = '001';
        $nomor_transaksi      = date('dmy').'1'.rand(pow(10, 2-1), pow(10, 2)-1).$last_id;

        $data          = [
            'title'              => 'Obat',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> Obat'],
            ],
            'header_title'       => 'Transaksi',
            'nomor_transaksi'    => $nomor_transaksi,
            'header_description' => 'Pembelian Langsung',
            'route'              => $this->_route,
            'jenis'              => 'langsung',
            'flash_message'      => view('_flash_message', []),
            'kategori'           => Kategori::where('status','!=',9)->get()->toJson(),
            'supplier'           => Supplier::where('status','!=',9)->get()->toArray(),
        ];
        
        return view("transaksi/pembelian_reguler", $data);

    }

    /**
     * serve ajax request
     * @return mixed
     */
    function remote()
    {
        if (isPost() && isAjax()) {
            switch (post('action')) {
                case 'cari-obat-popup':
                    $obat = Obat::where('status','!=',9);

                    if (post('keyword')!='') {
                        $post = post();
                        $obat = $obat->where(function($q) use ($post){
                            $q->where('nama','like', '%' . post('keyword') . '%');
                            $q->orWhere('kode','like', '%' . post('keyword') . '%');
                        });
                    }

                    $obat = $obat->take(20)->orderBy('created_at', 'desc')->get();
                    
                    $obat = $obat->toArray();

                    $data = [];
                    $i    = 0;
                    foreach ($obat as $key => $value) {

                        $kategori = Kategori::where('id',$value['kategori'])->first();
                        $data[] = [
                            'number'             => ++$i,
                            'kode'               => $value['kode'],
                            'nama'               => $value['nama'],
                            'kategori'           => isset($kategori->nama) ? $kategori->nama : '-',
                            'harga_jual_satuan'  => 'Rp.'. number_format($value['harga_jual_satuan'],0,'.','.'),
                            'harga_jual_resep'   => 'Rp.'. number_format($value['harga_jual_resep'],0,'.','.'),
                            'harga_jual_pack'   => 'Rp.'. number_format($value['harga_jual_pack'],0,'.','.'),
                            'satuan'  => $value['satuan'],
                            'stok'               => $value['stok'],
                            'action'             => '<button onclick="cariObat('.$value['kode'].')" type="button" class="btn btn-success"><i class="fa fa-check"></i></button>',
                        ];
                    }

                    $column = array(
                        array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
                        array('header' => 'Kode Obat', 'data' => 'kode', 'width' => '250px'),
                        array('header' => 'Nama Obat', 'data' => 'nama', 'width' => '250px'),
                        array('header' => 'Kategori', 'data' => 'kategori', 'width' => '250px'),
                        array('header' => 'Harga Jual Satuan', 'data' => 'harga_jual_satuan', 'width' => '250px'),
                        array('header' => 'Harga Jual Resep', 'data' => 'harga_jual_resep', 'width' => '250px'),
                        array('header' => 'Harga Jual Pack', 'data' => 'harga_jual_pack', 'width' => '250px'),
                        array('header' => 'Satuan', 'data' => 'satuan', 'width' => '250px'),
                        array('header' => 'Stok', 'data' => 'stok', 'width' => '150px'),
                        array('header' => 'Pilih', 'data' => 'action', 'width' => '50px'),
                    );

                    $table = $this->table->create_list(['class' => 'table'], $data, $column);
                    
                    return $table;

                    break;
                case 'cari-obat':
                    $obat = Obat::where('kode',post('id'))->with('kategori')->first();

                    if(!$obat)
                        echo json_encode(['data' => null]);
                    else 
                        echo json_encode(['data' => $obat->toArray()]);
                    break;
                case 'simpan-pembelian':
                        $pembelian = new Pembelian();
                        $pembelian->id_supplier = post('supplier') != '' ? (int)post('supplier') : null;
                        $pembelian->jumlah = post('jumlah');
                        $pembelian->total_harga = post('total_harga');
                        $pembelian->tanggal = post('tanggal');
                        $pembelian->jenis = post('jenis');
                        $pembelian->no_transaksi = post('no_transaksi');
                        $pembelian->save();

                        return json_encode(['status'=>'sukses','id'=>$pembelian->id]);
                    break;
                case 'simpan-transaksi':
                        $transaksi = new TransaksiPembelian();
                        $transaksi->id_pembelian = post('id_pembelian');
                        $transaksi->kode_obat = post('kode_obat');
                        $transaksi->total = post('total');
                        $transaksi->jumlah = post('jumlah_obat');
                        $transaksi->total_harga = post('total');
                        $transaksi->save();

                        $obat = Obat::where('kode',post('kode_obat'))->where('status','!=',9)->first();
                        if($obat)
                        {
                            $obat->stok = $obat->stok + (int)post('jumlah_obat');
                            $obat->update();
                        }else {
                            $obat = new Obat();
                            $obat->nama = post('nama_obat');
                            $obat->kode = post('kode_obat');
                            $obat->kategori = post('kategori_obat');
                            $obat->tgl_kadaluarsa = date('Y-m-d',strtotime('+3 year'));
                            $obat->satuan = post('satuan_obat');
                            $obat->type = post('type_obat');
                            $obat->stok = post('jumlah_obat') == '' ? 0 : (int)post('jumlah_obat');
                            $obat->save();
                        }

                        return json_encode(['status'=>'sukses']);
                    break;
                default:
                    break;
            }
        }
    }

}
