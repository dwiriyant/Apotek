<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Libraries\Table;
use App\Config;
use Auth;

class StokMinimalController extends Controller
{
    private $_page   = 1;
    private $_limit  = 25;
    private $_search = [];
    private $_route  = 'stok_minimal';
    
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
        $stok_minimal      = false;
        $this->_page = request()->page;

        if (isPost()) {
            $v = $this->validator(post());
            $v->rule('required', ['nama']);

            // end validation
            if ($v->validate()) 
                return $this->_form();
        }

        if (request('id')) {
            $stok_minimal = Config::where('id',(int)request('id'))->first();
        }

        if (isAjax()) {
            $param = $this->_search;
            $param['page'] = $this->_page;

            $param = array_filter($param);

            if($stok_minimal)
                $stok_minimal = $stok_minimal->toArray();

            echo $this->_form($stok_minimal, $param);

            return;
        }

        $param   = [];

        $stok_minimal = new Config;

        $count = $stok_minimal->where('nama','stok_minimal')->count();

        $this->_page = get('page', 1);

        $maxPage = ceil($count / $this->_limit);
            if ($maxPage < $this->_page)
                $this->_page = $maxPage;

        if((int)$this->_page == 0)
            $this->_page = 1;

        $offset = offset((int)$this->_page, $this->_limit); 

        $stok_minimal = $stok_minimal->take(1)->get();
        
        $stok_minimal = $stok_minimal->toArray();

        $data = [];
        $i    = $offset;
        foreach ($stok_minimal as $key => $value) {

            $data[] = [
                'number'         => ++$i,
                'stok_minimal'   => $value['value'],
                'action'         => view('master/stok_minimal/_action', ['param' => $param, 'stok_minimal' => $value, 'route' => $this->_route, 'column' => 'action'])
            ];
        }
        
        $column = array(
            array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
            array('header' => 'Stok Minimal', 'data' => 'stok_minimal', 'width' => '250px'),
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
            'title'              => 'Pengaturan Stok Minimal',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> Stok Minimal'],
            ],
            'header_title'       => 'Master',
            'header_description' => 'Stok Minimal',
            'table'              => $table,
            'route'              => $this->_route,
            'total'              => $count,
            'offset'             => $count == 0 ? -1 : $offset,
            'search'             => $this->_search,
            'limit'              => $this->_limit,
            'pagination'         => $pagination,
            'flash_message'      => view('_flash_message', []),
            'param'              => $param,
            'form'               => $this->_form($stok_minimal, $param),
        ];
        
        return view("master/stok_minimal/index", $data);

    }

    function _form($row = false, $param = [])
    {
        $errors  = [];
        // form is submitted
        if (isPost()) {
            // start validation
            $v = $this->validator(post());
            $v->rule('required', ['stok']);

            // end validation
            if ($v->validate()) {
                $data = $this->_save(post());

                if (isset($data['id'])) {

                    return redirect($this->_route)->with('success','Success <strong>' . (post('id') ? 'UPDATE' : 'ADD NEW') . '</strong> stok_minimal.');

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
            'stok_minimal'      => $row,
            'errors'        => $errors,
            'route'         => $this->_route,
            'param'         => $param,
        ];

        return view('master/stok_minimal/_form', $data);
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
     * delete stok_minimal
     * @return redirect to page
     */
    function delete()
    {
        $id = get('id');
        if ($id) {
            $stok_minimal = stok_minimal::where('id',(int)$id)->first();
            $stok_minimal->status = 9;
            $stok_minimal->save();

            if ($stok_minimal) {
                return redirect($this->_route)->with('success','Success <strong>DELETE</strong> stok_minimal.');
            }
        } else
        {
            return redirect($this->_route)->with('error','Failes <strong>DELETE</strong> stok_minimal.');
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

        $stok_minimal = config::where('id',(int)@$post['id'])->first();

        if(!$stok_minimal)
            $stok_minimal = new stok_minimal();
        
        $stok_minimal->value = $post['stok'];
        
        $stok_minimal->save();

        return $stok_minimal->toArray();
    }

}
