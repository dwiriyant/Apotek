<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Libraries\Table;
use App\Obat;
use App\Kategori;

class HomeController extends Controller
{
    private $_page   = 1;
    private $_limit  = 25;
    private $_search = [];
    private $_route  = 'dashboard';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $obat = Obat::where('status','!=',9)->where('stok','<=',5);

        $param = [];
        $count = $obat->count();

        $this->_page = get('page', 1);
       
        $maxPage = ceil($count / $this->_limit);
            if ($maxPage < $this->_page)
                $this->_page = $maxPage;

        if((int)$this->_page == 0)
            $this->_page = 1;

        $offset = offset((int)$this->_page, $this->_limit); 

        $obat = $obat->skip($offset)->take($this->_limit)->orderBy('created_at', 'desc')->get();
        
        $obat = $obat->toArray();

        $data = [];
        $i    = $offset;
        foreach ($obat as $key => $value) {

            $kategori = Kategori::where('id',$value['kategori'])->first();
            $data[] = [
                'number'             => ++$i,
                'kode'               => $value['kode'],
                'nama'               => $value['nama'],
                'kategori'           => isset($kategori->nama) ? $kategori->nama : '-',
                'tgl_kadaluarsa'     => date('d-m-Y', strtotime($value['tgl_kadaluarsa'])),
                'harga_jual_satuan'  => 'Rp.'. number_format($value['harga_jual_satuan'],0,'.','.'),
                'harga_jual_resep'   => 'Rp.'. number_format($value['harga_jual_resep'],0,'.','.'),
                'harga_jual_pack'   => 'Rp.'. number_format($value['harga_jual_pack'],0,'.','.'),
                'type'             => $value['type'] == 1 ? 'Sendiri' : 'Konsinyasi',
                'satuan'             => $value['satuan'],
                'stok'               => $value['stok'],
                'action'             => '<a target="_blank" title="PO" href="'.route('pembelian-po','po').'?kode='.$value['kode'].'" class="btn btn-sm btn-default"><i class="fa fa-paper-plane"> Requset PO</i></a>'
            ];
        }

        $column = array(
            array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
            array('header' => 'Kode Obat', 'data' => 'kode', 'width' => '250px'),
            array('header' => 'Nama Obat', 'data' => 'nama', 'width' => '250px'),
            array('header' => 'Kategori', 'data' => 'kategori', 'width' => '250px'),
            array('header' => 'Tanggal Kadaluarsa', 'data' => 'tgl_kadaluarsa', 'width' => '250px'),
            array('header' => 'Harga Jual Satuan', 'data' => 'harga_jual_satuan', 'width' => '250px'),
            array('header' => 'Harga Jual Resep', 'data' => 'harga_jual_resep', 'width' => '250px'),
            array('header' => 'Harga Jual Pack', 'data' => 'harga_jual_pack', 'width' => '250px'),
            array('header' => 'Status', 'data' => 'type', 'width' => '250px'),
            array('header' => 'Satuan', 'data' => 'satuan', 'width' => '250px'),
            array('header' => 'Stok', 'data' => 'stok', 'width' => '250px'),
            
            array('header' => 'Action', 'data' => 'action', 'width' => '120px')
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
            'title'              => 'Home',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard']
            ],
            'header_title'       => 'Dashboard',
            'header_description' => '',
            'table'              => $table,
            'total'              => $count,
            'offset'             => $count == 0 ? -1 : $offset,
            'limit'              => $this->_limit,
            'pagination'         => $pagination,
        ];
        return view('home', $data);
    }

}
