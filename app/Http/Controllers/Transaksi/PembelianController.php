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
        
        if($len>=4)
            $last_id = substr($last_id,$len-4,4);
        else if($len==3)
            $last_id = '0'.$last_id;
        else if($len==2)
            $last_id = '00'.$last_id;
        else if($len==1)
            $last_id = '000'.$last_id;
        else
            $last_id = '0001';
        $nomor_transaksi      = date('dmy').rand(pow(10, 2-1), pow(10, 2)-1).$last_id;

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
                        $pembelian->save();

                        return json_encode(['status'=>'sukses','id'=>$pembelian->id]);
                    break;
                case 'simpan-transaksi':
                        vd(post());
                        $transaksi = new TransaksiPembelian();
                        $transaksi->id_pembelian = post('id_pembelian');
                        $transaksi->kode_obat = post('kode_obat');
                        $transaksi->total = post('total');
                        $transaksi->jumlah = post('jumlah_obat');
                        $transaksi->total_harga = post('total_harga');
                        $transaksi->save();

                        $obat = Obat::where('kode',post('kode_obat'))->first();
                        if($obat)
                        {
                            $obat->stok = $obat->stok - post('jumlah');
                            $obat->update();
                        }else {
                            $obat = new Obat();
                            $obat->nama = post('nama_obat');
                            $obat->kode = post('kode_obat');
                            $obat->kategori = post('kategori_obat');
                            $obat->tgl_kadaluarsa = date('Y-m-d',strtotime('+3 year'));
                            $obat->satuan = post('satuan_obat');
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
