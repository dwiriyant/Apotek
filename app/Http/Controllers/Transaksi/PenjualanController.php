<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Obat;
use App\Penjualan;
use App\Customer;
use App\Dokter;
use App\TransaksiPenjualan;
use Auth;

class PenjualanController extends Controller
{
    private $_route  = 'penjualan-reguler';
    
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
    public function index($resep = '')
    {
        if($resep == 'resep')
            $resep = true;
        else if($resep != '')
            abort(404);
        $last_id = (string)Penjualan::max('id');
        $len = strlen($last_id);
        if($len>4)
            $last_id = substr($last_id,$len-3,3);
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
            'header_description' => 'Penjualan Reguler',
            'route'              => $this->_route,
            'jenis'              => $resep ? 'resep' : 'langsung',
            'flash_message'      => view('_flash_message', []),
            'customer'           => Customer::where('status','!=',9)->get()->toArray(),
            'dokter'           => Dokter::where('status','!=',9)->get()->toArray(),
        ];
        
        return view("transaksi/penjualan_reguler", $data);

    }

    function _form($row = false, $param = [])
    {
        $errors  = [];
        // form is submitted
        if (isPost()) {
            // start validation
            $v = $this->validator(post());
            $v->rule('required', ['nama','kode','kategori','harga_satuan']);

            // end validation
            if ($v->validate()) {

                $data = $this->_save(post());

                if (isset($data['id'])) {

                    return redirect($this->_route)->with('success','Success <strong>' . (post('id') ? 'UPDATE' : 'ADD NEW') . '</strong> obat.');

                } else {
                    return redirect($this->_route)->with('error','Failed to save obat.');
                }
            }
            $row = post();

            // set error
            $errors = $v->errors();
        }

        // prepare form data
        $data = [
            'obat'      => $row,
            'errors'        => $errors,
            'route'         => $this->_route,
            'param'         => $param,
            'kategori'      => Kategori::where('status','!=',9)->get()->toArray()
        ];

        return view('master/obat/_form', $data);
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
                    $obat = Obat::where('kode',(int)post('id'))->with('kategori')->first();

                    if(!$obat)
                        echo json_encode(['data' => null]);
                    else 
                        echo json_encode(['data' => $obat->toArray()]);
                    break;
                case 'simpan-penjualan':
                        $penjualan = new Penjualan();
                        $penjualan->jumlah = post('jumlah');
                        post('jenis') == 'resep' ? $penjualan->id_dokter = post('dokter') : null;
                        $penjualan->id_konsumen = post('customer') ? post('customer') : null;
                        $penjualan->uang = post('uang');
                        $penjualan->diskon = post('diskon');
                        $penjualan->total_harga = post('total_harga');
                        $penjualan->tanggal = post('tanggal');
                        $penjualan->jenis = post('jenis');
                        $penjualan->save();

                        return json_encode(['status'=>'sukses','id'=>$penjualan->id]);
                    break;
                case 'simpan-transaksi':
                        $transaksi = new TransaksiPenjualan();
                        $transaksi->id_penjualan = post('id_penjualan');
                        $transaksi->kode_obat = post('kode_obat');
                        $transaksi->total = post('total');
                        $transaksi->jumlah = post('jumlah');
                        $transaksi->total_harga = post('total_harga');
                        $transaksi->save();

                        $obat = Obat::where('kode',(int)post('kode_obat'))->first();
                        if($obat)
                        {
                            $obat->stok = $obat->stok - post('jumlah');
                            $obat->update();
                        }

                        return json_encode(['status'=>'sukses']);
                    break;
                default:
                    break;
            }
        }
    }

}
