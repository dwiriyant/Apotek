<?php

namespace App\Http\Controllers\Retur;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Libraries\Table;
use PHPExcel as PHPExcelces; 
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use App\Penjualan;
use App\ReturPenjualan;
use App\Kategori;
use App\Obat;
use App\TransaksiPenjualan;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class PenjualanController extends Controller
{
    private $_page   = 1;
    private $_limit  = 25;
    private $_search = [];
    private $_route  = 'retur-penjualan';
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Table $table)
    {
        $this->middleware('auth');
        $this->table = $table;
        $this->_search = [
            'konsumen'     => trim(get('konsumen')),
            'dokter'   => trim(get('dokter')),
            'jenis'   => trim(get('jenis')),
            'start'   => trim(get('start')),
            'end'   => trim(get('end')),
            'no_transaksi'   => trim(get('no_transaksi')),
        ];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $penjualan      = false;
        $this->_page = request()->page;

        if (request('id')) {
            $penjualan = Penjualan::where('id',(int)request('id'))->first();
        }
        $search = $this->_search;

        $param   = [];

        $penjualan = Penjualan::where('status','!=',9);

        if ($search['start']!='' && $search['end']!='') {
            $penjualan = $penjualan->whereBetween('tanggal', [$search['start'], date('Y-m-d', strtotime($search['end'] . '+1day'))]);
            $param   = [
                'start'   => $this->_search['start'],
                'end'   => $this->_search['end'],
            ];
        }

        if ($search['no_transaksi']!='') {
            $penjualan = $penjualan->where('no_transaksi',$search['no_transaksi']);
            $param   = [
                'no_transaksi'   => $this->_search['no_transaksi']
            ];
        }

        $count = $penjualan->count();

        $this->_page = get('page', 1);
       
        $maxPage = ceil($count / $this->_limit);
            if ($maxPage < $this->_page)
                $this->_page = $maxPage;

        if((int)$this->_page == 0)
            $this->_page = 1;

        $offset = offset((int)$this->_page, $this->_limit); 

        $penjualan = $penjualan->with('customer')->with('dokter');
        $penjualan = $penjualan->skip($offset)->take($this->_limit)->orderBy('created_at', 'desc')->get();
        
        $penjualan = $penjualan->toArray();

        $data = [];
        $i    = $offset;
        foreach ($penjualan as $key => $value) {
            $data[] = [
                'number'             => ++$i,
                'no'            => $value['no_transaksi'],
                'customer'           => isset($value['customer']['nama']) ? $value['customer']['nama'] : '-',
                'dokter'           => isset($value['dokter']['nama']) ? $value['dokter']['nama'] : '-',
                'jumlah'            => $value['jumlah'],
                'jenis'         => $value['jenis'],
                'tanggal'     => date('d M Y H:i',strtotime($value['tanggal'])),
                'harga'         => 'Rp.'. number_format($value['total_harga'],0,'.','.'),
                'action'    => '<button data-id="'.$value['id'].'" type="button" class="btn btn-sm btn-info get-detail"><i class="fa fa-eye">Detail</i></button>'
            ];
        }

        $column = array(
            array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
            array('header' => 'No Transaksi', 'data' => 'no', 'width' => '250px'),
            array('header' => 'Tanggal Penjualan', 'data' => 'tanggal', 'width' => '250px'),
            array('header' => 'Jumlah', 'data' => 'jumlah', 'width' => '250px'),
            array('header' => 'Total Harga', 'data' => 'harga', 'width' => '250px'),
            array('header' => 'Pelanggan', 'data' => 'customer', 'width' => '250px'),
            array('header' => 'Dokter', 'data' => 'dokter', 'width' => '250px'),
            array('header' => 'Jenis Transaksi', 'data' => 'jenis', 'width' => '250px'),
            array('header' => 'Detail', 'data' => 'action', 'width' => '100px'),
        );

        $table = $this->table->create_list(['class' => 'table'], $data, $column);

        $pagination_config = array(
            'total_rows' => $count,
            'page'       => $this->_page,
            'per_page'   => $this->_limit,
            'class'      => 'pull-right',
            'base_url'   => url($this->_route, $param),
        );
        $pagination        = $this->table
            ->set_pagination($pagination_config)
            ->link_pagination();

        $param['page'] = $this->_page;
        $data          = [
            'title'              => 'Penjualan',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> Report Penjualan'],
            ],
            'header_title'       => 'Report',
            'header_description' => 'Penjualan',
            'table'              => $table,
            'route'              => $this->_route,
            'total'              => $count,
            'offset'             => $count == 0 ? -1 : $offset,
            'search'             => $this->_search,
            'kategori'           => Kategori::where('status','!=',9)->get()->toArray(),
            'limit'              => $this->_limit,
            'pagination'         => $pagination,
            'flash_message'      => view('_flash_message', []),
            'param'              => $param,
        ];
        
        return view("retur/penjualan", $data);

    }

    function remote()
    {
        if (isPost() && isAjax()) {
            switch (post('action')) {
                case 'get-transaction':
                    $transaksi = TransaksiPenjualan::where('status','!=',9)->where('id_penjualan',post('id'));

                    $transaksi = $transaksi->with('obat')->get()->toArray();
                    
                    $data = [];
                    $i    = 0;
                    foreach ($transaksi as $key => $value) {

                        $kategori = Kategori::where('id',$value['obat']['kategori'])->first();
                        $data[] = [
                            'number'             => ++$i,
                            'kode'               => $value['obat']['kode'],
                            'nama'               => $value['obat']['nama'],
                            'kategori'           => isset($kategori->nama) ? $kategori->nama : '-',
                            'harga_satuan'  => 'Rp.'. number_format($value['total'],0,'.','.'),
                            'satuan'  => $value['obat']['satuan'],
                            'type'               => $value['obat']['type'] == 1 ? 'Sendiri' : 'Konsinyasi',
                            'jumlah' => $value['jumlah'],
                            'total' => 'Rp.'. number_format($value['total_harga'],0,'.','.'),
                            'retur'         => '<input data-id="'.$value['id'].'" type="number" id="retur-'.$value['id'].'" class="retur input-sm form-control"/>',
                            'keterangan'         => '<input data-id="'.$value['id'].'" data-kode="'.$value['obat']['kode'].'" type="text" id="keterangan-'.$value['id'].'" class="keterangan input-sm form-control"/> 
                            <small id="keterangan-note-'.$value['id'].'" class="text-muted" style="display:none;">Tekan ENTER utk menyimpan.</small>'
                        ];
                    }

                    $column = array(
                        array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
                        array('header' => 'Kode Obat', 'data' => 'kode', 'width' => '250px'),
                        array('header' => 'Nama Obat', 'data' => 'nama', 'width' => '250px'),
                        array('header' => 'Kategori', 'data' => 'kategori', 'width' => '250px'),
                        array('header' => 'Satuan', 'data' => 'satuan', 'width' => '250px'),
                        array('header' => 'Status', 'data' => 'type', 'width' => '250px'),
                        array('header' => 'Harga Satuan', 'data' => 'harga_satuan', 'width' => '250px'),
                        array('header' => 'Jumlah', 'data' => 'jumlah', 'width' => '150px'),
                        array('header' => 'Total', 'data' => 'total', 'width' => '250px'),
                        array('header' => 'Retur', 'data' => 'retur', 'width' => '250px'),
                        array('header' => 'Keterangan', 'data' => 'keterangan', 'width' => '250px'),
                    );

                    $table = $this->table->create_list(['class' => 'table'], $data, $column);
                    
                    return $table;

                    break;
                case 'retur':
                    $post = post();

                    $obat = Obat::where('kode',(int)@$post['kode'])->first();

                    $trans = TransaksiPenjualan::where('id',$post['id'])->with('penjualan')->first();
                    if($trans)
                        $trans = $trans->toArray();
                    $no_transaksi = (int)@$trans['penjualan']['no_transaksi'];

                    $stok = new ReturPenjualan();
                    $stok->id_transaksi = (int) $post['id'];
                    $stok->no_transaksi = $no_transaksi;
                    $stok->jumlah = (int) $post['retur'];
                    $stok->keterangan = $post['keterangan'];
                    $stok->operator = Auth::user()->name;
                    $stok->save();

                    $obat->stok = $obat->stok + (int) $post['retur'];
                    $obat->save();

                break;
                default:
                    break;
            }
        }
    }

    /**
     * build search url then redirect
     * @return redirect
     */
    function search()
    {
        if (isPost()) {
            $param     = [];
            $paramable = ['start','end','no_transaksi'];
            foreach ($paramable as $key => $value) {
                $post = post($value);
                if ($post!='')
                    $param[$value] = $post;
            }
            return redirect()->route($this->_route, $param);
        }
    }

}
