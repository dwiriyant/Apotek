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
            'start'   => trim(get('start')),
            'end'   => trim(get('end')),
            'status'   => trim(get('status')),
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
            $v->rule('required', ['name','start_date','end_date']);

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

        if ($search['name']!='') {
            $where['name'] = ['$regex' => $search['name'],'$options' => 'i'];
            $param   = [
                'name'   => $this->_search['name'],
            ];
        }

        $count = $obat->count();

        $this->_page = get('page', 1);
       // var_dump($data);die();
        $maxPage = ceil($count / $this->_limit);
            if ($maxPage < $this->_page)
                $this->_page = $maxPage;

        if((int)$this->_page == 0)
            $this->_page = 1;

        $offset = offset((int)$this->_page, $this->_limit); 

        if (get('export', false)) {
            if($this->_page == 1)
            {
                $datas = $obat->whereRaw($where)->orderBy('created_at', 'desc')->get();
            }
            else
            {
                $datas = $obat->whereRaw($where)->skip($offset)->take($this->_limit)->orderBy('created_at', 'desc')->get();
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
            $data[] = [
                'number'             => ++$i,
                'kode'               => $value['kode'],
                'nama'               => $value['nama'],
                'kategori'           => $value['kategori'],
                'tgl_kadaluarsa'     => $value['tgl_kadaluarsa'],
                'harga_jual_satuan'  => $value['harga_jual_satuan'],
                'harga_jual_resep'   => $value['harga_jual_resep'],
                'harga_jual_grosir'  => $value['harga_jual_grosir'],
                'stok'               => $value['stok'],
                'action'             => view('master/obat/_action', ['param' => $param, 'obat' => $value, 'route' => $this->_route, 'column' => 'action'])
            ];
        }

        $column = array(
            array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
            array('header' => 'Kode Obat', 'data' => 'kode', 'width' => '250px'),
            array('header' => 'Nama Obat', 'data' => 'nama', 'width' => '250px'),
            array('header' => 'Kategori', 'data' => 'kategori', 'width' => '250px'),
            array('header' => 'Tanggal Kadaluarsa', 'tgl_kadaluarsa' => 'nama', 'width' => '250px'),
            array('header' => 'Harga Jual Satuan', 'harga_jual_satuan' => 'nama', 'width' => '250px'),
            array('header' => 'Harga Jual Resep', 'harga_jual_resep' => 'nama', 'width' => '250px'),
            array('header' => 'Harga Jual Grosir', 'harga_jual_grosir' => 'nama', 'width' => '250px'),
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
            'title'              => 'obat Schedule',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> obat Schedule'],
            ],
            'header_title'       => 'Content',
            'header_description' => 'obat Schedule',
            'table'              => $table,
            'route'              => $this->_route,
            'total'              => $count,
            'offset'             => $offset == 0 ? $offset = -1 : $offset,
            'search'             => $this->_search,
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
                    set_flash('Failed to save obat.');
                    set_flash('error', 'status');
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
            $paramable = ['name','status','start', 'end'];
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

        if($obat)
        {
            $obat->nama = $post['nama'];
            $obat->kode = $post['kode'];
            $obat->kategori = $post['kategori'];
            $obat->tgl_kadaluarsa = $post['tgl_kadaluarsa'];
            $obat->harga_jual_satuan = $post['harga_satuan'] == '' ? 0 : $post['harga_satuan'];
            $obat->harga_jual_resep = $post['harga_resep'] == '' ? 0 : $post['harga_resep'];
            $obat->harga_jual_grosir = $post['harga_grosir'] == '' ? 0 : $post['harga_grosir'];
            $obat->stok = (int)$post['stok'];
        } else {

            $obat = new Obat();
            $obat->nama = $post['nama'];
            $obat->kode = $post['kode'];
            $obat->kategori = $post['kategori'];
            $obat->tgl_kadaluarsa = $post['tgl_kadaluarsa'];
            $obat->harga_jual_satuan = $post['harga_satuan'] == '' ? 0 : $post['harga_satuan'];
            $obat->harga_jual_resep = $post['harga_resep'] == '' ? 0 : $post['harga_resep'];
            $obat->harga_jual_grosir = $post['harga_grosir'] == '' ? 0 : $post['harga_grosir'];
            $obat->stok = (int)$post['stok'];
        }
        
        
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
            $_title = 'First - '.$get['end'];
        else
            $_title = '';

        $report_title = 'Report Content obat';
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
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Report Content');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->getFont()->setBold(true);
        $i++;$i++;

        $columns = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

        $abj = 'A';
        
        $objPHPExcel->getActiveSheet()->setTitle('Report Content obat');

        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'No.');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'News Title');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Shedule');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Submit Date');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Author');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Active');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Editor Pick');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Editor Pick Last Checked Date');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Headline');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Headline Last Checked Date');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Type');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Source');
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
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['news_title']);
            // Set hyperlink
            $objPHPExcel->getActiveSheet()->getCell($abj.$i)->getHyperlink()->setUrl($value['news_url']);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($link_style_array);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, @$value['news_date_publish'] && $value['news_level']==2 ? date('d M Y H:i',$value['news_date_publish']) : '-');
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, @$value['news_entry'] ? date('d M Y H:i',$value['news_entry']) : '-');
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;

            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, @$value['member_id']);
            if(@$value['member_url'])
            {
                $objPHPExcel->getActiveSheet()->getCell($abj.$i)->getHyperlink()->setUrl($value['member_url']);
                $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($link_style_array);
            } else
            {
                $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            }
            $abj++;

            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['news_level'] == 2 ? 'Active' : 'inactive');
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['news_editor_pick'] == '1' ? 'Yes' : 'No');
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, (isset($value['date_editor_pick']) ? date('d/m/Y H:i',$value['date_editor_pick']) : ''));
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, @$value['news_headline'] == '0' ? 'No' : 'Yes');
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, (isset($value['date_headline']) ? date('d/m/Y H:i',$value['date_headline']) : ''));
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;

            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, @$value['type'] ? ucfirst($value['type']) : 'Video');
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;

            $_source = @$value['manual_input'] ? 'input-' : 'crawl-';

            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, @$value['source'] ? $_source.$value['source'] : 'Submit');
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

}
