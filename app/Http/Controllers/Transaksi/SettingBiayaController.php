<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Libraries\Table;
use App\SettingBiaya;
use Auth;

class SettingBiayaController extends Controller
{
    private $_page   = 1;
    private $_limit  = 25;
    private $_search = [];
    private $_route  = 'setting-biaya';
    
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
        $setting_biaya      = false;
        $this->_page = request()->page;

        if (isPost()) {
            $v = $this->validator(post());
            $v->rule('required', ['nama']);

            // end validation
            if ($v->validate()) 
                return $this->_form();
        }

        if (request('id')) {
            $setting_biaya = SettingBiaya::where('id',(int)request('id'))->first();
        }

        if (isAjax()) {
            $param = $this->_search;
            $param['page'] = $this->_page;

            $param = array_filter($param);

            if($setting_biaya)
                $setting_biaya = $setting_biaya->toArray();

            echo $this->_form($setting_biaya, $param);

            return;
        }

        $param   = [];

        $setting_biaya = SettingBiaya::where('status','!=',9);

        if ($this->_search['name']!='') {
            $setting_biaya = $setting_biaya->where('nama','like', '%' . $this->_search['name'] . '%');
            $param   = [
                'name'   => $this->_search['name'],
            ];
        }

        $count = $setting_biaya->count();

        $this->_page = get('page', 1);

        $maxPage = ceil($count / $this->_limit);
            if ($maxPage < $this->_page)
                $this->_page = $maxPage;

        if((int)$this->_page == 0)
            $this->_page = 1;

        $offset = offset((int)$this->_page, $this->_limit); 

        $setting_biaya = $setting_biaya->skip($offset)->take($this->_limit)->orderBy('created_at', 'desc')->get();
        
        $setting_biaya = $setting_biaya->toArray();


        $data = [];
        $i    = $offset;
        foreach ($setting_biaya as $key => $value) {

            $data[] = [
                'number'         => ++$i,
                'name'           => $value['nama'],
                'deskripsi'      => $value['deskripsi'],
                'biaya'          => 'Rp.'. number_format($value['biaya'],0,'.','.'),
                'periode'        => date('d-m-Y', strtotime($value['periode'])),
                'action'         => view('transaksi/setting_biaya/_action', ['param' => $param, 'setting_biaya' => $value, 'route' => $this->_route, 'column' => 'action'])
            ];
        }

        $column = array(
            array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
            array('header' => 'Nama Biaya', 'data' => 'name', 'width' => '250px'),
            array('header' => 'Deskripsi', 'data' => 'deskripsi', 'width' => '250px'),
            array('header' => 'Biaya', 'data' => 'biaya', 'width' => '250px'),
            array('header' => 'Periode', 'data' => 'periode', 'width' => '250px'),

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
            'title'              => 'Setting Biaya',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> Setting Biaya'],
            ],
            'header_title'       => 'Transaksi',
            'header_description' => 'Setting Biaya',
            'table'              => $table,
            'route'              => $this->_route,
            'total'              => $count,
            'offset'             => $count == 0 ? -1 : $offset,
            'search'             => $this->_search,
            'limit'              => $this->_limit,
            'pagination'         => $pagination,
            'flash_message'      => view('_flash_message', []),
            'param'              => $param,
            'form'               => $this->_form($setting_biaya, $param),
        ];
        
        return view("transaksi/setting_biaya/index", $data);

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

                    return redirect($this->_route)->with('success','Success <strong>' . (post('id') ? 'UPDATE' : 'ADD NEW') . '</strong> setting biaya.');

                } else {
                    return redirect($this->_route)->with('error','Failed to save setting biaya.');
                }
            }
            $row = post();

            // set error
            $errors = $v->errors();
        }
        
        // prepare form data
        $data = [
            'setting_biaya' => $row,
            'errors'        => $errors,
            'route'         => $this->_route,
            'param'         => $param,
        ];

        return view('transaksi/setting_biaya/_form', $data);
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
            $setting_biaya = SettingBiaya::where('id',(int)$id)->first();
            $setting_biaya->status = 9;
            $setting_biaya->save();

            if ($setting_biaya) {
                return redirect($this->_route)->with('success','Success <strong>DELETE</strong> setting biaya.');
            }
        } else
        {
            return redirect($this->_route)->with('error','Failes <strong>DELETE</strong> setting biaya.');
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

        $setting_biaya = SettingBiaya::where('id',(int)@$post['id'])->first();

        if(!$setting_biaya)
            $setting_biaya = new SettingBiaya();
        
        $setting_biaya->nama = $post['nama'];
        $setting_biaya->deskripsi = $post['deskripsi'];
        $setting_biaya->periode = $post['periode'];
        $setting_biaya->biaya = (int) $post['biaya'];
        
        $setting_biaya->save();

        return $setting_biaya->toArray();
    }

}
