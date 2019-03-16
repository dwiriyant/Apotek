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
        $html =' <html>
            <head><style>
                .table { display: table; width: 100%; border-collapse: collapse; }
                .table-row { display: table-row; }
                .table-cell { display: table-cell; border: 1px solid black; padding: 1em; }
            }
            span {  display: block;  }
            @page table {
            size: 340px 650px;
            margin: 0px;;
            }

            .table {
            page: table;
            page-break-after: always;
            font-size: 20px;
            }
            </style>
            </head>
            <body>
            <div class="table">
            <div class="table-row"><div class="table-cell" colspan="3" style="text-align: center"><img src="../../img/top-logo.png"></div></div>
                <div class="table-row">
                <div class="table-cell" ><span><b> Merchant: </b> '.@$parceldetails['company'].' </span><span><b> Pick Addr: </b> '.@$parceldetails['addr'].' </span><span><b> Mobile: </b> '.@$parceldetails['mobile'].' </span></div>
                <div class="table-cell" style="padding: 0px">
                <div class="" >Delivery Date:</div><br>
                <div class="" style="border-bottom: 1px solid #000000"> '.@$parceldetails['r_delivery_time'].' at '.@$parceldetails['bytime'].'</div>

                <div class="">Agent:</div><br>
                <div class=""> '.@$parceldetails['name'].' </div>
                </div>
                </div>
                <div class="table-row">
                <div class="table-cell" colspan="3" style="text-align: center"> <b style="font-size: larger">'.@$ecr.'</b></div>
                </div>
                <div class="table-row">
                <div class="table-cell" colspan="1"><span><b>Customer Name:</b> '.@$parceldetails['r_name'].'</span><span><b> Addr:</b> '.@$parceldetails['r_address'].' </span><span><b> Mobile: </b> '.@$parceldetails['r_mobile'].' </span></div>
                <div class="table-cell" style="padding: 0px">
                    <div class="" style="border-bottom: 2px solid #000000; text-align: center"><b> '.@$parceldetails['paymentmethod'].' </b></div>
                    <div class="" style="text-align: center"><b> '.@$parceldetails['product_price'].' BDT </b></div>
                </div>
                </div>
                <div class="table-row">
                <div class="table-cell"  style="text-align: center"> zzzz </div>
                    <div class="table-cell"  style="padding: 0px">
                    <div class="" style="border-bottom: 2px solid #000000; text-align: center; height:63px"> Delivered </div>
                    <div class="" style="text-align: center; min-height:63px"> Cancel </div>
                </div>
                    <div class="table-cell" style="padding: 0px">
                    <div class="" style="border-bottom: 2px solid #000000; text-align: center; height:63px">&#160;</div>
                    <div class="" style="text-align: center; min-height:63px""></div>
                </div>
                </div>
                <div class="table-row">
                <div class="table-cell" colspan="3">
                <b style="margin-top:50px; margin-bottom:-10px; border-bottom: 1px solid #000000; font-size:10px; margin-left:10px">Agent signature</b>
                <b style="margin-top:50px; margin-bottom:-10px; border-bottom: 1px solid #000000; font-size:10px; margin-left:50px">Receiver signature</b></div>
                </div>
            </div>';
            $html .='<table class="table">
            <tr>
            <td colspan="3"><img src="../../img/top-logo.png"></td>
            </tr>
            <tr>
                <td rowspan="2" colspan="2"><span><b> Merchant: </b> '.@$parceldetails['company'].' </span><span><b> Pick Addr: </b> '.@$parceldetails['addr'].' </span><span><b> Mobile: </b> '.@$parceldetails['mobile'].' </span></td>
                <td>D. Date<span>'.@$parceldetails['r_delivery_time'].'</span></td>
            </tr>
            <tr>
                <td>Agent<span>'.@$parceldetails['name'].'</span></td>
            </tr>
            <tr>
                <td colspan="3">'.@$ecr.'</td>
            </tr>
                <tr>
                <td rowspan="2" colspan="2"><span><b>Customer Name:</b> '.@$parceldetails['r_name'].'</span><span><b> Addr:</b> '.@$parceldetails['r_address'].' </span><span><b> Mobile: </b> '.@$parceldetails['r_mobile'].' </span></td>
                <td><b>'.@$parceldetails['paymentmethod'].'</b></td>
            </tr>
            <tr>
                <td><b>'.@$parceldetails['product_price'].' BDT</b></td>
            </tr>
            <tr>
                <td rowspan="2" colspan="1">zzz</td>
                <td>Delivered</td>
                <td></td>
            </tr>
            <tr>
                <td>Cancel</td>
                <td></td>
            </tr>
                <tr>
                <td colspan="3">&nbsp</td>
            </tr>
                <tr>
                <td colspan="3">Agent Signature Receiver Signature</td>
            </tr>
            </table>
            <script type="text/javascript"> 
            this.print(true) 
            </script> 
            </body>
            </html>';
        
        $dompdf = PDF::loadHTML($html);
        $dompdf->setOptions(['defaultFont' =>'Courier']);

        $customPaper = array(0,0,340,650);
        // $dompdf->set_paper($customPaper);
        // $dompdf->setOptions('enable_css_float',true);
        
        //$dompdf->set_paper("A3", "portrait");

        $dompdf->setPaper(array(0,0,204,650));
        // $dompdf->set_option('dpi', 72);
        // $dompdf->render();
        return $dompdf->stream("dompdf_out.pdf", array('Attachment' => 0));
    }

}
