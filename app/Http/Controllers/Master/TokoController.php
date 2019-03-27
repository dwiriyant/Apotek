<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Libraries\Table;
use App\Toko;
use Auth;

class TokoController extends Controller
{
    private $_page   = 1;
    private $_limit  = 25;
    private $_search = [];
    private $_route  = 'toko';
    
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
        ];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $toko      = false;
        $this->_page = request()->page;

        if (isPost()) {
            $v = $this->validator(post());
            $v->rule('required', ['nama']);

            // end validation
            if ($v->validate()) 
                return $this->_form();
        }

        if (request('id')) {
            $toko = Toko::where('id',(int)request('id'))->first();
        }

        if (isAjax()) {
            $param = $this->_search;
            $param['page'] = $this->_page;

            $param = array_filter($param);

            if($toko)
                $toko = $toko->toArray();

            echo $this->_form($toko, $param);

            return;
        }

        $param   = [];

        $toko = new Toko;

        $count = $toko->count();

        $this->_page = get('page', 1);

        $maxPage = ceil($count / $this->_limit);
            if ($maxPage < $this->_page)
                $this->_page = $maxPage;

        if((int)$this->_page == 0)
            $this->_page = 1;

        $offset = offset((int)$this->_page, $this->_limit); 

        $toko = $toko->skip($offset)->take($this->_limit)->get();
        
        $toko = $toko->toArray();

        $data = [];
        $i    = $offset;
        foreach ($toko as $key => $value) {

            $data[] = [
                'number'         => ++$i,
                'name'           => $value['nama'],
                'alamat'           => $value['alamat'],
                'no_telp'           => $value['no_telp'],
                'action'         => view('master/toko/_action', ['param' => $param, 'toko' => $value, 'route' => $this->_route, 'column' => 'action'])
            ];
        }

        $column = array(
            array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
            array('header' => 'Nama Apotek', 'data' => 'name', 'width' => '250px'),
            array('header' => 'Alamat Apotek', 'data' => 'alamat', 'width' => '250px'),
            array('header' => 'No Telp Apotek', 'data' => 'no_telp', 'width' => '250px'),
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
            'title'              => 'Apotek',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> Apotek'],
            ],
            'header_title'       => 'Master',
            'header_description' => 'Apotek',
            'table'              => $table,
            'route'              => $this->_route,
            'total'              => $count,
            'offset'             => $count == 0 ? -1 : $offset,
            'search'             => $this->_search,
            'limit'              => $this->_limit,
            'pagination'         => $pagination,
            'flash_message'      => view('_flash_message', []),
            'param'              => $param,
            'form'               => $this->_form($toko, $param),
        ];
        
        return view("master/toko/index", $data);

    }

    function _form($row = false, $param = [])
    {
        $errors  = [];
        // form is submitted
        if (isPost()) {
            // start validation
            $v = $this->validator(post());
            $v->rule('required', ['nama','alamat','no_telp']);

            // end validation
            if ($v->validate()) {
                $data = $this->_save(post());

                if (isset($data['id'])) {

                    return redirect($this->_route)->with('success','Success <strong>' . (post('id') ? 'UPDATE' : 'ADD NEW') . '</strong> toko.');

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
            'toko'      => $row,
            'errors'        => $errors,
            'route'         => $this->_route,
            'param'         => $param,
        ];

        return view('master/toko/_form', $data);
    }

    /**
     * build search url then redirect
     * @return redirect
     */
    function search()
    {
        if (isPost()) {
            $param     = [];
            $paramable = ['name'];
            foreach ($paramable as $key => $value) {
                $post = post($value);
                if ($post!='')
                    $param[$value] = $post;
            }
            return redirect()->route($this->_route, $param);
        }
    }

    /**
     * delete toko
     * @return redirect to page
     */
    function delete()
    {
        $id = get('id');
        if ($id) {
            $toko = toko::where('id',(int)$id)->first();
            $toko->status = 9;
            $toko->save();

            if ($toko) {
                return redirect($this->_route)->with('success','Success <strong>DELETE</strong> toko.');
            }
        } else
        {
            return redirect($this->_route)->with('error','Failes <strong>DELETE</strong> toko.');
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
                default:
                    break;
            }
        }
    }

    function _save($post = [])
    {
        if(empty($post))
            return;

        $toko = toko::where('id',(int)@$post['id'])->first();

        if(!$toko)
            $toko = new toko();
        
        $toko->nama = $post['nama'];
        $toko->alamat = $post['alamat'];
        $toko->no_telp = $post['no_telp'];
        
        $toko->save();

        return $toko->toArray();
    }

}
