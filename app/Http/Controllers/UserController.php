<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Libraries\Table;
use Auth;
use App\User;

class UserController extends Controller
{
	private $_search = [];
	private $_limit = 25;
	private $_page = 1;

	public function __construct(Table $table, Request $request)
    {
        $this->middleware('auth');
        $this->table = $table;
        $this->_search   = [
            'name'    => trim(request()->name),
            'username'    => trim(request()->username),
            'email'    => trim(request()->email),
            'phone'    => trim(request()->phone),
            'level'    => trim(request()->level),
        ];
    }
    
	public function FUNC_LIST(){
        if(Auth::user()->level != 1)
            abort(403);
		$this->_page = request()->page ? request()->page : 1;

		$data = User::where('status','!=',9);

		$search = $this->_search;
		if ($search['name']!='') {
			$data = $data->where('name','like', '%' .$search['name']. '%');
        }
        if ($search['username']!='') {
            $data = $data->where('username','like', '%' .$search['username']. '%');
        }
        if ($search['email']!='') {
            $data = $data->where('email','like', '%' .$search['email']. '%');
        }
        if ($search['phone']!='') {
            $data = $data->where('phone','like', '%' .$search['phone']. '%');
        }
        if ($search['level']!='') {
            $data = $data->where('level',(int)$search['level']);
        }
        $count = clone $data;
        $count = $count->count();

        $maxPage = ceil($count / $this->_limit);
        if ($maxPage < $this->_page)
            $this->_page = $maxPage;
        $offset = offset($this->_page, $this->_limit);
        if($offset < 0)
            $offset = 0;

        $data   = $data->take($this->_limit)
            ->skip($offset)
            ->orderBy('created_at', 'desc')
            ->get();

		$param = [];
        foreach ($search as $key => $value)
            if ($value)
                $param[$key] = $value;

        $pagination_config = array(
            'total_rows'      => $count,
            'page'            => $this->_page,
            'total_side_link' => 3,
            'per_page'        => $this->_limit,
            'class'           => 'pull-right',
            'base_url'        => route('user', $param),
        );
        $pagination        = $this->table
            ->set_pagination($pagination_config)
            ->link_pagination();

        $param['page'] = $this->_page;

        $data = [
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> User'],
            ],
            'header_title'       => 'User',
            'header_description' => 'List User',
        	'data' => $data,
        	'search' => $search,
        	'pagination' => $pagination,
        	'limit' => $this->_limit,
        	'total' => $count,
        	'offset' => $offset,
        	'no' => $offset+1
        ];

		return view('user.list',$data);
	}
	public function FUNC_ADD(){
        if(Auth::user()->level != 1)
            abort(403);
        $data = [
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> User'],
            ],
            'header_title'       => 'User',
            'header_description' => 'Add User',
        ];
		return view('user.add',$data);
	}
	public function FUNC_SAVE(Request $request){
		$this->validate($request, [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed',
            'phone' => 'required',
            'level' => 'required'
        ]);

		$add = new User();
		$add->name = $request['name'];
		$add->username = $request['username'];
		$add->email = $request['email'];
		$add->password = Hash::make($request['password']);
		$add->phone = $request['phone'];
        $add->level = (int)$request['level'];

		$add->save();

		return redirect('listuser')->with('success','data behasil disimpan');
	}
	public function FUNC_EDIT($id){
        if(Auth::user()->level != 1)
            abort(403);
		$data = User::where('id',$id)->first();

        $data->breadcrumb = [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> User'],
            ];
        $data->header_title = 'User';
        $data->header_description = 'List User';
		
		if(!empty($data))
			return view('user.edit',compact('data'));
		else
			abort(404);
	}
	public function FUNC_UPDATE(Request $request, $id){
		if($request['password'])
			$pass_val = 'required|confirmed';
		else
			$pass_val = '';
		$this->validate($request, [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'password' => $pass_val,
            'phone' => 'required',
            'level' => 'required'
        ]);
		
		$add = User::where('id',$id)->first();
		$add->name = $request['name'];
		$add->username = $request['username'];
		$add->email = $request['email'];
		if($request['password'])
			$add->password = Hash::make($request['password']);
		$add->phone = $request['phone'];
        $add->level = (int)$request['level'];

		$add->update();

		return redirect('listuser')->with('success','data behasil diupdate');
	}
	public function FUNC_DELETE($id){
        if(Auth::user()->level != 1)
            abort(403);
		$delete = User::find($id);
		$delete->status = 9;
        $delete->update();

		return redirect('listuser')->with('success','data behasil dihapus');
	}

	public function FUNC_SEARCH(Request $request)
    {
        if(Auth::user()->level != 1)
            abort(403);
        if ($request->isMethod('post')) {
            $param     = [];
            $paramable = ['name','username','email','phone','level'];

            foreach ($paramable as $key => $value) {
                $post = $request[$value];
                if($post!='')
                    $param[$value] = $post;
            }
            
            return redirect()->route('user', $param);
        }
    }

}