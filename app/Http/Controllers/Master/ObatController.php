<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Libraries\Table;
use PHPExcel as PHPExcelces; 
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use App\Obat;
use App\Kategori;
use Auth;
use App\Imports\ObatImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ObatController extends Controller
{
    private $_page   = 1;
    private $_limit  = 25;
    private $_search = [];
    private $_route  = 'obat';
    
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
            $v->rule('required', ['nama','kode','kategori','harga_satuan']);

            // end validation
            if ($v->validate()) 
                return $this->_form();
        }

        if (request('id')) {
            $obat = Obat::where('id',(int)request('id'))->first();
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
        $search = $this->_search;

        $param   = [];

        $obat = Obat::where('status','!=',9);

        if ($search['kode']!='') {
            $obat = $obat->where('kode','like', '%' . $this->_search['kode'] . '%');
            $param   = [
                'kode'   => $this->_search['kode'],
            ];
        }

        if ($search['satuan']!='') {
            $obat = $obat->where('satuan',$this->_search['satuan']);
            $param   = [
                'satuan'   => $this->_search['satuan'],
            ];
        }

        if ($search['name']!='') {
            $obat = $obat->where('nama','like', '%' . $this->_search['name'] . '%');
            $param   = [
                'name'   => $this->_search['name'],
            ];
        }

        if ($search['kategori']!='') {
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

        if (get('export', false)) {
            if($this->_page == 1)
            {
                $datas = $obat->orderBy('created_at', 'desc')->get();
            }
            else
            {
                $datas = $obat->skip($offset)->take($this->_limit)->orderBy('created_at', 'desc')->get();
            }
            $datas = $datas->toArray();
            $this->export_content($datas,get());
            return;
        }

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
                'satuan'  => $value['satuan'],
                'stok'               => $value['stok'],
                'action'             => view('master/obat/_action', ['param' => $param, 'obat' => $value, 'route' => $this->_route, 'column' => 'action'])
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
            'title'              => 'Obat',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> Obat'],
            ],
            'header_title'       => 'Master',
            'header_description' => 'Obat',
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
            'form'               => $this->_form($obat, $param),
        ];
        
        return view("master/obat/index", $data);

    }

    function _form($row = false, $param = [])
    {
        $errors  = [];
        // form is submitted
        if (isPost()) {
            // start validation
            $v = $this->validator(post());
            $v->rule('required', ['nama','kode','kategori','harga_satuan']);

            // end validation
            if ($v->validate()) {

                $data = $this->_save(post());

                if (isset($data['id'])) {

                    return redirect($this->_route)->with('success','Success <strong>' . (post('id') ? 'UPDATE' : 'ADD NEW') . '</strong> obat.');

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
            'obat'      => $row,
            'errors'        => $errors,
            'route'         => $this->_route,
            'param'         => $param,
            'kategori'      => Kategori::where('status','!=',9)->get()->toArray()
        ];

        return view('master/obat/_form', $data);
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
     * delete obat
     * @return redirect to referer page
     */
    function delete()
    {
        $id = get('id');
        if ($id) {
            $obat = obat::where('id',(int)$id)->first();
            $obat->level = 9;
            $obat->save();

            if ($obat) {
                 return redirect($this->_route)->with('success','Success <strong>DELETE</strong> obat.');
            }
        } else
        {
            return redirect($this->_route)->with('error','Failes <strong>DELETE</strong> obat.');
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
                case 'getObatByKode':
                    $kode = post('kode');
                    $result = Obat::where('kode', $kode)->first();
                    return json_encode($result);
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

        $obat = Obat::where('id',(int)@$post['id'])->first();

        if(!$obat)
            $obat = new Obat();
        $obat->nama = $post['nama'];
        $obat->kode = $post['kode'];
        $obat->kategori = $post['kategori'] == '' ? 0 : (int)$post['kategori'];
        $obat->tgl_kadaluarsa = $post['tgl_kadaluarsa'];
        $obat->harga_jual_satuan = $post['harga_satuan'] == '' ? 0 : (int)$post['harga_satuan'];
        $obat->harga_jual_resep = $post['harga_resep'] == '' ? 0 : (int)$post['harga_resep'];
        $obat->satuan = $post['satuan'];
        $obat->stok = $post['stok'] == '' ? 0 : (int)$post['stok'];
        
        $obat->save();

        return $obat->toArray();
    }

    function export_content($data,$get)
    {
        if(@$get['start'] && @$get['end'])
            $_title = $get['start'].' - '.$get['end'];
        elseif(@$get['start'])
            $_title = $get['start'].' - '.date('d M Y');
        elseif(@$get['end'])
            $_title = 'Sampai tgl '.$get['end'];
        else
            $_title = '';

        $report_title = 'Report Data Obat '. $_title;
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcelces();
        // Set properties
        $objPHPExcel->getProperties()->setCreator("Brilio.net");
        $objPHPExcel->getProperties()->setLastModifiedBy("Brilio.net");
        $objPHPExcel->getProperties()->setTitle("Office XLS");
        $objPHPExcel->getProperties()->setSubject("Office XLS");
        $objPHPExcel->getProperties()->setDescription($report_title.", generated using PHP classes.");

        // Add some data
        // set header
        $objPHPExcel->setActiveSheetIndex(0);
        // config
        $style_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        
        $styleHeader = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'D3D3D3')
                ),
            );
        //set border
        $style = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        $link_style_array = array(
            'font'  => array(
                'color' => ['rgb' => '0000FF'],
                'underline' => 'single'
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
        );
        
        $abj = 'A';
        $i   = 1;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $report_title);
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->getFont()->setBold(true);
        $i++;$i++;

        $columns = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

        $abj = 'A';
        
        $objPHPExcel->getActiveSheet()->setTitle('Report Data Obat');

        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'No.');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Kode Obat');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Nama Obat');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Kategori Obat');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Tanggal Kadaluarsa');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Harga Jual Satuan');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Harga Jual Resep');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Satuan');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Stok');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        // Rename sheet
        $i++;
        foreach ($data as $key => $value) 
        {     
            $abj = 'A';
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $key+1);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;

            $kategori = Kategori::where('id',$value['kategori'])->first();

            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['kode']);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['nama']);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, isset($kategori->nama) ? $kategori->nama : '-');
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, date('d-m-Y', strtotime($value['tgl_kadaluarsa'])));
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Rp.'. number_format($value['harga_jual_satuan'],0,'.','.'));
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Rp.'. number_format($value['harga_jual_resep'],0,'.','.'));
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['satuan']);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['stok']);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            
            $i++;
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);


        //End Sheet User Agent        

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel5)

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$report_title.'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        $this->end();
        
    }

    public function import(Request $request)
    {
        $uploadedFile = $request->file('file');
        $filename = time() . '-' . $uploadedFile->getClientOriginalName();
        $path = Storage::disk('local')->putFileAs(
            'files/Obat',
            $uploadedFile,
            $filename
          );
        $filepath = storage_path('app') . '/' . $path;
        Excel::import(new ObatImport, $filepath);
        
        return redirect('/obat')->with('success', 'All good!');
    }

}
