<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Click;
use App\News;

class HomeController extends Controller
{
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
    public function index()
    {
        $data          = [
            'title'              => 'Home',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard']
            ],
            'header_title'       => 'Dashboard',
            'header_description' => '',
        ];
        return view('home', $data);
    }

}
