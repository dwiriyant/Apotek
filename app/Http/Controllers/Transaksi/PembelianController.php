<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Obat;
use App\ObatPO;
use PDF;
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
    public function index($po = '')
    {
        if($po == 'po')
            $po = true;
        else if($po != '')
            abort(404);

        $pos = $po;
        $approve = false;
        if(get('approve') && $po == '')
        {
            $approve = get('approve');
            $pos = 'po';
        }

        $kode = null;
        if(get('kode') && $kode == '')
            $kode = get('kode');

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

        $pembelian = [];
        if($approve)
        {
            $pembelian = Pembelian::where('no_transaksi',$approve)->where('status',2)->with('supplier')->with('transaksiPo')->first();
            if($pembelian)
            {
                $pembelian = $pembelian->toArray();
                $nomor_transaksi = $pembelian['no_transaksi'];
            } else {
                abort(404);
            }
        }
        
        $data          = [
            'title'              => 'Pembelian',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> Pembelian'],
            ],
            'header_title'       => 'Transaksi',
            'nomor_transaksi'    => $nomor_transaksi,
            'header_description' => 'Pembelian '. ($pos ? 'PO' : 'Langsung'),
            'route'              => $this->_route,
            'jenis'              => $po ? 'po' : 'langsung',
            'flash_message'      => view('_flash_message', []),
            'kategori'           => Kategori::where('status','!=',9)->get()->toJson(),
            'supplier'           => Supplier::where('status','!=',9)->get()->toArray(),
            'pembelian'          => $pembelian,
            'kode'               => $kode
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
                        $pembelian = Pembelian::where('no_transaksi',post('no_transaksi'))->where('status',2)->first();
                        if($pembelian)
                        {
                            $pembelian->status = 1;

                        } else {
                            $pembelian = new Pembelian();
                            $pembelian->no_transaksi = post('no_transaksi');
                            if(post('jenis') == 'po')
                                $pembelian->status = 2;
                            $pembelian->jenis = post('jenis');
                        }
                        
                        $pembelian->id_supplier = post('supplier') != '' ? (int)post('supplier') : null;
                        $pembelian->jumlah = post('jumlah');
                        $pembelian->total_harga = post('total_harga');
                        $pembelian->tanggal = post('tanggal');
                        
                        
                        $pembelian->save();

                        return json_encode(['status'=>'sukses','id'=>$pembelian->id]);
                    break;
                case 'simpan-transaksi':
                        $jenis = post('jenis');    
                        $return_po = false;
                        if($jenis == 'langsung')
                            $transaksi = TransaksiPembelian::where('id_pembelian',post('id_pembelian'))->where('kode_obat',post('kode_obat'))->where('status',2)->first();
                        else
                            $transaksi = false;
                        
                        if($transaksi){
                            $jenis = 'po';
                            $transaksis = clone($transaksi);
                            $return_po = true;
                        }
                        else
                            $transaksis = $transaksi;
                        if($transaksis)
                        {
                            $transaksi->status = 1;

                        } else {
                            $transaksi = new TransaksiPembelian();
                            $transaksi->id_pembelian = post('id_pembelian');

                            if ($jenis == 'po')
                            {
                                $return_po = true;
                                $transaksi->status = 2;
                                
                                $obat = new ObatPO();
                                $obat->id_pembelian = post('id_pembelian');
                                $obat->nama = post('nama_obat');
                                $obat->kode = post('kode_obat');
                                $obat->kategori = post('kategori_obat');
                                $obat->tgl_kadaluarsa = date('Y-m-d',strtotime('+3 year'));
                                $obat->satuan = post('satuan_obat');
                                $obat->type = post('type_obat');
                                $obat->stok = post('jumlah_obat') == '' ? 0 : (int)post('jumlah_obat');
                                $obat->save();
                            }
                            $transaksi->id_obat_po = isset($obat->id) ? $obat->id : 0;
                        }
                        
                        $transaksi->kode_obat = post('kode_obat');
                        $transaksi->total = post('harga');
                        $transaksi->jumlah = post('jumlah_obat');
                        $transaksi->total_harga = post('total');
                        
                        $transaksi->save();

                        if($jenis == 'langsung' || $transaksis)
                        {                                
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
                        } 

                        return json_encode(['status'=>'sukses','po' => $return_po]);
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

        $pembelian = Pembelian::where('no_transaksi',$transaksi)->with('supplier')->first();
        if($pembelian)
            $pembelian = $pembelian->toArray();
        else 
            abort(404);

        $transaksi = TransaksiPembelian::where('id_pembelian',$pembelian['id'])->with('obat_po')->get();
        if($transaksi)
            $transaksi = $transaksi->toArray();
        else 
            abort(404);
        
        $data['pembelian'] = $pembelian;
        $data['transaksi'] = $transaksi;
        
        return view("pdf.po", $data);
    }

}
