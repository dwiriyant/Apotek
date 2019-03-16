<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Libraries\Table;
use App\SettingBiaya;
use Auth;
use PHPExcel as PHPExcelces; 
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

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
            'start'   => trim(get('start')),
            'end'   => trim(get('end'))
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

        if ($this->_search['start'] !='' || $this->_search['end'] !='') {
            $setting_biaya = $setting_biaya->whereBetween('periode', [$this->_search['start'], date('Y-m-d', strtotime($this->_search['end'] . '+1day'))]);
            $param   = [
                'start'   => $this->_search['start'],
                'end'   => $this->_search['end'],
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

        if (get('export', false)) {
            if($this->_page == 1)
            {
                $datas = $setting_biaya->orderBy('created_at', 'desc')->get();
            }
            else
            {
                $datas = $setting_biaya->skip($offset)->take($this->_limit)->orderBy('created_at', 'desc')->get();
            }
            $datas = $datas->toArray();
            $this->export_content($datas,get());
            return;
        }

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
            $paramable = ['start','end'];
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

        $report_title = 'Report Data Biaya '. $_title;
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
        
        $objPHPExcel->getActiveSheet()->setTitle('Report Biaya Pengeluaran');

        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'No.');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Nama Biaya');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Deskripsi');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Biaya');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Periode');
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

            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['nama']);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['deskripsi']);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['biaya']);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['periode']);
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
}
