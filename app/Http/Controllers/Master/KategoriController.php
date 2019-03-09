<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Libraries\Table;
use App\Kategori;
use Auth;

class KategoriController extends Controller
{
    private $_page   = 1;
    private $_limit  = 25;
    private $_search = [];
    private $_route  = 'kategori';
    
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
        $kategori      = false;
        $this->_page = request()->page;

        if (isPost()) {
            $v = $this->validator(post());
            $v->rule('required', ['nama']);

            // end validation
            if ($v->validate()) 
                return $this->_form();
        }

        if (request('id')) {
            $kategori = Kategori::where('id',(int)request('id'))->first();
        }

        if (isAjax()) {
            $param = $this->_search;
            $param['page'] = $this->_page;

            $param = array_filter($param);

            if($kategori)
                $kategori = $kategori->toArray();

            echo $this->_form($kategori, $param);

            return;
        }

        $param   = [];

        $kategori = Kategori::where('status','!=',9);

        if ($this->_search['name']!='') {
            $kategori = $kategori->where('nama','like', '%' . $this->_search['name'] . '%');
            $param   = [
                'name'   => $this->_search['name'],
            ];
        }

        $count = $kategori->count();

        $this->_page = get('page', 1);

        $maxPage = ceil($count / $this->_limit);
            if ($maxPage < $this->_page)
                $this->_page = $maxPage;

        if((int)$this->_page == 0)
            $this->_page = 1;

        $offset = offset((int)$this->_page, $this->_limit); 

        $kategori = $kategori->skip($offset)->take($this->_limit)->orderBy('created_at', 'desc')->get();
        
        $kategori = $kategori->toArray();


        $data = [];
        $i    = $offset;
        foreach ($kategori as $key => $value) {

            $data[] = [
                'number'         => ++$i,
                'name'           => $value['nama'],
                'action'         => view('master/kategori/_action', ['param' => $param, 'kategori' => $value, 'route' => $this->_route, 'column' => 'action'])
            ];
        }

        $column = array(
            array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
            array('header' => 'Nama Kategori', 'data' => 'name', 'width' => '250px'),
            
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
            'title'              => 'Kategori',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> Kategori'],
            ],
            'header_title'       => 'Master',
            'header_description' => 'Kategori',
            'table'              => $table,
            'route'              => $this->_route,
            'total'              => $count,
            'offset'             => $count == 0 ? -1 : $offset,
            'search'             => $this->_search,
            'limit'              => $this->_limit,
            'pagination'         => $pagination,
            'flash_message'      => view('_flash_message', []),
            'param'              => $param,
            'form'               => $this->_form($kategori, $param),
        ];
        
        return view("master/kategori/index", $data);

    }

    function _form($row = false, $param = [])
    {
        $errors  = [];
        // form is submitted
        if (isPost()) {
            // start validation
            $v = $this->validator(post());
            $v->rule('required', ['nama']);

            // end validation
            if ($v->validate()) {
                $data = $this->_save(post());

                if (isset($data['id'])) {

                    return redirect($this->_route)->with('success','Success <strong>' . (post('id') ? 'UPDATE' : 'ADD NEW') . '</strong> kategori.');

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
            'kategori'      => $row,
            'errors'        => $errors,
            'route'         => $this->_route,
            'param'         => $param,
        ];

        return view('master/kategori/_form', $data);
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
     * delete kategori
     * @return redirect to page
     */
    function delete()
    {
        $id = get('id');
        if ($id) {
            $kategori = kategori::where('id',(int)$id)->first();
            $kategori->status = 9;
            $kategori->save();

            if ($kategori) {
                return redirect($this->_route)->with('success','Success <strong>DELETE</strong> kategori.');
            }
        } else
        {
            return redirect($this->_route)->with('error','Failes <strong>DELETE</strong> kategori.');
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

        $kategori = kategori::where('id',(int)@$post['id'])->first();

        if(!$kategori)
            $kategori = new kategori();
        
        $kategori->nama = $post['nama'];
        
        $kategori->save();

        return $kategori->toArray();
    }

}
