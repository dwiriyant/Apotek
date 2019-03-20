<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Libraries\Table;
use PDF;
use App\Obat;
use App\Penjualan;
use App\Customer;
use App\Dokter;
use App\Kategori;
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
    public function __construct(Table $table)
    {
        $this->middleware('auth');
        $this->table = $table;
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
            'header_description' => 'Penjualan '.$resep ? 'Resep' : 'Reguler',
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
                            'type'               => $value['type'] == 1 ? 'Sendiri' : 'Konsinyasi',
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
                        array('header' => 'Status', 'data' => 'type', 'width' => '250px'),
                        array('header' => 'Stok', 'data' => 'stok', 'width' => '150px'),
                        array('header' => 'Pilih', 'data' => 'action', 'width' => '50px'),
                    );

                    $table = $this->table->create_list(['class' => 'table'], $data, $column);
                    
                    return $table;

                    break;
                case 'cari-obat':
                    $obat = Obat::where('kode',post('id'))->where('status','!=',9)->with('kategori')->first();

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
                        $penjualan->total = post('total');
                        $penjualan->diskon = post('diskon');
                        $penjualan->total_harga = post('total_harga');
                        $penjualan->tanggal = post('tanggal');
                        $penjualan->jenis = post('jenis');
                        $penjualan->no_transaksi = post('no_transaksi');
                        $penjualan->save();

                        return json_encode(['status'=>'sukses','id'=>$penjualan->id]);
                    break;
                case 'simpan-transaksi':
                        $transaksi = new TransaksiPenjualan();
                        $transaksi->id_penjualan = post('id_penjualan');
                        $transaksi->kode_obat = post('kode_obat');
                        $transaksi->total = post('total');
                        $transaksi->jumlah = post('jumlah_obat');
                        $transaksi->total_harga = post('total');
                        $transaksi->save();

                        $obat = Obat::where('kode',post('kode_obat'))->where('status','!=',9)->first();
                        if($obat)
                        {
                            $obat->stok = $obat->stok - (int)post('jumlah_obat');
                            $obat->update();
                        }

                        return json_encode(['status'=>'sukses']);
                    break;
                default:
                    break;
            }
        }
    }

    function print()
    {        
        $transaksi = get('transaksi');
        if($transaksi=='')
            abort(404);

        $penjualan = Penjualan::where('no_transaksi',$transaksi)->with('customer')->with('dokter')->first();
        if($penjualan)
            $penjualan = $penjualan->toArray();
        else 
            abort(404);

        $transaksi = TransaksiPenjualan::where('id_penjualan',$penjualan['id'])->with('obat')->get();
        if($transaksi)
            $transaksi = $transaksi->toArray();
        else 
            abort(404);

        $data['penjualan'] = $penjualan;
        $data['transaksi'] = $transaksi;
        $data['data'] = $data;


        $GLOBALS['bodyHeight'] = 0;

        $dompdf = PDF::loadView('pdf.struk', $data);
        $dompdf->setPaper(array(0,0,204,650));
        $dompdf->setOptions(['defaultFont' =>'Courier']);
        $dompdf = $dompdf->getDomPDF();
        $dompdf->setCallbacks(
        array(
            'myCallbacks' => array(
            'event' => 'end_frame', 'f' => function ($infos) {
                $frame = $infos["frame"];
                if (strtolower($frame->get_node()->nodeName) === "body") {
                    $padding_box = $frame->get_padding_box();
                    $GLOBALS['bodyHeight'] += $padding_box['h'];
                }
            }
            )
        )
        );
        $dompdf->render();

        unset($dompdf);

        $dompdf = PDF::loadView('pdf.struk', $data);
        $dompdf->setPaper(array(0,0,204,$GLOBALS['bodyHeight']+60));

        $dompdf->setOptions(['dpi' => 72]);

        return $dompdf->stream();
    }

}
