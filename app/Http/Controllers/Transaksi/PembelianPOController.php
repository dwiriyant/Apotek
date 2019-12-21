<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Libraries\Table;
use PHPExcel as PHPExcelces; 
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use App\Pembelian;
use App\Kategori;
use App\Supplier;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class PembelianPOController extends Controller
{
    private $_page   = 1;
    private $_limit  = 25;
    private $_search = [];
    private $_route  = 'pembelian-reguler';
    private $_route2  = 'pembelian-reguler/po/dashboard';
    
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
            'supplier'   => trim(get('supplier')),
        ];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $pembelian      = false;
        $this->_page = request()->page;

        $search = $this->_search;

        $param   = [];

        $pembelian = Pembelian::where('status',2);
        
        if ($search['start']!='' && $search['end']!='') {
            $pembelian = $pembelian->whereBetween('tanggal', [$search['start'], date('Y-m-d', strtotime($search['end'] . '+1day'))]);
            $param   = [
                'start'   => $this->_search['start'],
                'end'   => $this->_search['end'],
            ];
        }

        if ($search['no_transaksi']!='') {
            $pembelian = $pembelian->where('no_transaksi',$search['no_transaksi']);
            $param   = [
                'no_transaksi'   => $this->_search['no_transaksi']
            ];
        }

        if ($search['supplier']!='') {
            $pembelian = $pembelian->where('id_supplier',$search['supplier']);
            $param   = [
                'supplier'   => $this->_search['supplier']
            ];
        }

        $count = $pembelian->count();

        $this->_page = get('page', 1);
       
        $maxPage = ceil($count / $this->_limit);
            if ($maxPage < $this->_page)
                $this->_page = $maxPage;

        if((int)$this->_page == 0)
            $this->_page = 1;

        $offset = offset((int)$this->_page, $this->_limit); 

        $pembelian = $pembelian->with('supplier')->with('transaksiPo');
        $pembelian = $pembelian->skip($offset)->take($this->_limit)->orderBy('created_at', 'desc')->get();
        
        $pembelian = $pembelian->toArray();
        
        $data = [];
        $i    = $offset;
        foreach ($pembelian as $key => $value) {
            $data[] = [
                'number'             => ++$i,
                'no_transaksi'       =>$value['no_transaksi'],
                'supplier'           => isset($value['supplier']['nama']) ? $value['supplier']['nama'] : '-',
                'jumlah_transaksi'            => count($value['transaksi_po']),
                'action'            => '<div class="btn-group  btn-action-4">
                <a href="'.url($this->_route).'/print?transaksi='.$value['no_transaksi'].'" target="_blank">
                    <button type="button" class="btn btn-sm btn-default"><i class="fa fa-print">Print</i></button>
                </a>
                <a href="'.url($this->_route).'?approve='.$value['no_transaksi'].'">
                    <button type="button" class="btn btn-sm btn-success"><i class="fa fa-check">Approve</i></button>
                </a>
                <a href="'.route('dashboard-po-delete', ['id' => $value['id']]).'" class="confirm">
                    <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-check">Decline</i></button>
                </a>
                                        </div>',
                'tanggal'     => date('d M Y H:i',strtotime($value['tanggal'])),
            ];
        }

        $column = array(
            array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
            array('header' => 'No Transaksi', 'data' => 'no_transaksi', 'width' => '250px'),
            array('header' => 'Tanggal pembelian', 'data' => 'tanggal', 'width' => '250px'),
            array('header' => 'Jumlah Transaksi', 'data' => 'jumlah_transaksi', 'width' => '250px'),
            array('header' => 'Supplier', 'data' => 'supplier', 'width' => '250px'),
            array('header' => 'Action', 'data' => 'action', 'width' => '250px'),
        );

        $table = $this->table->create_list(['class' => 'table'], $data, $column);

        $pagination_config = array(
            'total_rows' => $count,
            'page'       => $this->_page,
            'per_page'   => $this->_limit,
            'class'      => 'pull-right',
            'base_url'   => url($this->_route2, $param),
        );
        $pagination        = $this->table
            ->set_pagination($pagination_config)
            ->link_pagination();

        $param['page'] = $this->_page;
        $data          = [
            'title'              => 'Transaksi',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> Pembelian'],
            ],
            'header_title'       => 'Transaksi',
            'header_description' => 'Pembelian PO',
            'table'              => $table,
            'route'              => $this->_route2,
            'total'              => $count,
            'offset'             => $count == 0 ? -1 : $offset,
            'search'             => $this->_search,
            'kategori'           => Kategori::where('status','!=',9)->get()->toArray(),
            'limit'              => $this->_limit,
            'pagination'         => $pagination,
            'flash_message'      => view('_flash_message', []),
            'param'              => $param,
            'supplier'           => Supplier::where('status','!=',9)->get()->toArray(),
        ];
        
        return view("transaksi/dashboard_po/pembelian", $data);

    }

    function delete()
    {
        $id = get('id');
        if ($id) {
            $obat = Pembelian::where('id',(int)$id)->first();
            $obat->status = 9;
            $obat->save();

            if ($obat) {
                 return redirect($this->_route2)->with('success','Success <strong>DELETE</strong> PO.');
            }
        } else
        {
            return redirect($this->_route2)->with('error','Failes <strong>DELETE</strong> PO.');
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
            $paramable = ['start','end','no_transaksi','supplier'];
            foreach ($paramable as $key => $value) {
                $post = post($value);
                if ($post!='')
                    $param[$value] = $post;
            }
            return redirect()->route('dashboard-po', $param);
        }
    }
}
