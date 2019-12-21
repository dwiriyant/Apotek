<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Libraries\Table;
use App\Obat;
use App\StokOpname;
use App\Kategori;
use Auth;
use PHPExcel as PHPExcelces; 
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class StokOpnameController extends Controller
{
    private $_page   = 1;
    private $_limit  = 25;
    private $_search = [];
    private $_route  = 'stok-opname';
    
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
            'name'     => trim(get('name')),
            'kode'   => trim(get('kode')),
            'satuan'   => trim(get('satuan')),
            'kategori'   => trim(get('kategori')),
        ];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $obat      = false;
        $this->_page = request()->page;

        if (isPost()) {
            $v = $this->validator(post());
            $v->rule('required', ['nama']);

            // end validation
            if ($v->validate()) 
                return $this->_form();
        }

        if (request('id')) {
            $obat = StokOpname::where('id',(int)request('id'))->first();
        }

        if (isAjax()) {
            $param = $this->_search;
            $param['page'] = $this->_page;

            $param = array_filter($param);

            if($obat)
                $obat = $obat->toArray();

            echo $this->_form($obat, $param);

            return;
        }

        $param   = [];

        $obat = Obat::where('status','!=',9);

        if ($this->_search['kode']!='') {
            $obat = $obat->where('kode','like', '%' . $this->_search['kode'] . '%');
            $param   = [
                'kode'   => $this->_search['kode'],
            ];
        }

        if ($this->_search['satuan']!='') {
            $obat = $obat->where('satuan',$this->_search['satuan']);
            $param   = [
                'satuan'   => $this->_search['satuan'],
            ];
        }

        if ($this->_search['name']!='') {
            $obat = $obat->where('nama','like', '%' . $this->_search['name'] . '%');
            $param   = [
                'name'   => $this->_search['name'],
            ];
        }

        if ($this->_search['kategori']!='') {
            $obat = $obat->where('kategori', (int)$this->_search['kategori']);
            $param   = [
                'kategori'   => $this->_search['kategori'],
            ];
        }

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
                'satuan'             => $value['satuan'],
                'stok'               => '<span id="stok-software-'.$value['id'].'">'.$value['stok'].'</span>',
                'stok_nyata'         => '<input data-id="'.$value['id'].'" type="number" id="stok-nyata-'.$value['id'].'" class="stok-nyata input-sm form-control"/>',
                'keterangan'         => '<input data-id="'.$value['id'].'" type="text" id="keterangan-'.$value['id'].'" class="keterangan input-sm form-control"/> 
                <small id="keterangan-note-'.$value['id'].'" class="text-muted" style="display:none;">Tekan ENTER agar Stok & keterangan berubah.</small>'
            ];
        }

        $column = array(
            array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
            array('header' => 'Kode Obat', 'data' => 'kode', 'width' => '200px'),
            array('header' => 'Nama Obat', 'data' => 'nama', 'width' => '200px'),
            array('header' => 'Kategori', 'data' => 'kategori', 'width' => '200px'),
            array('header' => 'Satuan', 'data' => 'satuan', 'width' => '200px'),
            array('header' => 'Stok Software', 'data' => 'stok', 'width' => '200px'),
            array('header' => 'Stok Nyata', 'data' => 'stok_nyata', 'width' => '250px'),
            array('header' => 'Keterangan', 'data' => 'keterangan', 'width' => '250px'),
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
            'title'              => 'Stok Opname',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> Stok Opname'],
            ],
            'header_title'       => 'Stok',
            'header_description' => 'Stok Opname',
            'table'              => $table,
            'route'              => $this->_route,
            'total'              => $count,
            'offset'             => $count == 0 ? -1 : $offset,
            'search'             => $this->_search,
            'limit'              => $this->_limit,
            'pagination'         => $pagination,
            'kategori'           => Kategori::where('status','!=',9)->get()->toArray(),
            'flash_message'      => '',
            'param'              => $param,
            'form'               => $this->_form($obat, $param)
        ];
        
        return view("stok_opname/index", $data);

    }

    function _form($row = false, $param = [])
    {
        $errors  = [];
        // form is submitted
        if (isPost()) {
            // start validation
            $v = $this->validator(post());
            $v->rule('required', ['id_obat']);

            // end validation
            if ($v->validate()) {
                $data = $this->_save(post());

                if (isset($data['id'])) {

                    return redirect($this->_route)->with('success','Success <strong>' . (post('id') ? 'UPDATE' : 'ADD NEW') . '</strong> stok opname.');

                } else {
                    return redirect($this->_route)->with('error','Failed to save stok opname.');
                }
            }
            $row = post();

            // set error
            $errors = $v->errors();
        }
        
        // prepare form data
        $data = [
            'obat'          => $row,
            'errors'        => $errors,
            'route'         => $this->_route,
            'param'         => $param,
        ];

        return view('stok_opname/_form', $data);
    }

    /**
     * build search url then redirect
     * @return redirect
     */
    function search()
    {
        if (isPost()) {
            $param     = [];
            $paramable = ['name','kode','kategori','satuan'];
            foreach ($paramable as $key => $value) {
                $post = post($value);
                if ($post!='')
                    $param[$value] = $post;
            }
            return redirect()->route($this->_route, $param);
        }
    }

    /**
     * serve ajax request
     * @return mixed
     */
    function remote()
    {
        if (isPost() && isAjax()) {
            switch (post('action')) {
                case 'updateStok':
                
                    $post = array('id_obat' => post('id_obat'), 'stok_nyata' => post('stok_nyata'),'keterangan' => post('keterangan'));

                    $this->_save($post);
                break;

                default:
                break;
            }
        }
    }

    function _save($post = [])
    {
        if(empty($post))
            return;

        $obat = Obat::where('id',(int)@$post['id_obat'])->first();

        $stok = new StokOpname();
        $stok->id_obat = (int) $post['id_obat'];
        $stok->stok_software = (int) $obat->stok;
        $stok->stok_nyata = (int) $post['stok_nyata'];
        $stok->keterangan = $post['keterangan'];
        $stok->operator = Auth::user()->name;
        $stok->save();

        $obat->stok = (int) $post['stok_nyata'];
        $obat->save();

        return $obat->toArray();
    }
}
