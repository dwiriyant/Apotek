<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Libraries\Table;
use PHPExcel as PHPExcelces; 
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use App\Penjualan;
use App\Kategori;
use App\TransaksiPenjualan;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class PenjualanController extends Controller
{
    private $_page   = 1;
    private $_limit  = 25;
    private $_search = [];
    private $_route  = 'report-penjualan';
    
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
            'konsumen'     => trim(get('konsumen')),
            'dokter'   => trim(get('dokter')),
            'jenis'   => trim(get('jenis')),
            'start'   => trim(get('start')),
            'end'   => trim(get('end')),
            'no_transaksi'   => trim(get('no_transaksi')),
        ];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $penjualan      = false;
        $this->_page = request()->page;

        if (request('id')) {
            $penjualan = Penjualan::where('id',(int)request('id'))->first();
        }
        $search = $this->_search;

        $param   = [];

        $penjualan = Penjualan::where('status','!=',9);

        if ($search['start']!='' && $search['end']!='') {
            $penjualan = $penjualan->whereBetween('tanggal', [$search['start'], date('Y-m-d', strtotime($search['end'] . '+1day'))]);
            $param   = [
                'start'   => $this->_search['start'],
                'end'   => $this->_search['end'],
            ];
        }

        if ($search['no_transaksi']!='') {
            $penjualan = $penjualan->where('no_transaksi',$search['no_transaksi']);
            $param   = [
                'no_transaksi'   => $this->_search['no_transaksi']
            ];
        }

        $count = $penjualan->count();

        $this->_page = get('page', 1);
       
        $maxPage = ceil($count / $this->_limit);
            if ($maxPage < $this->_page)
                $this->_page = $maxPage;

        if((int)$this->_page == 0)
            $this->_page = 1;

        $offset = offset((int)$this->_page, $this->_limit); 

        $penjualan = $penjualan->with('customer')->with('dokter')->with('transaksi');

        if (get('export', false)) {
            if($this->_page == 1)
            {
                $datas = $penjualan->orderBy('created_at', 'desc')->get();
            }
            else
            {
                $datas = $penjualan->skip($offset)->take($this->_limit)->orderBy('created_at', 'desc')->get();
            }
            $datas = $datas->toArray();
            $this->export_content($datas,get());
            return;
        }
        
        $penjualan = $penjualan->skip($offset)->take($this->_limit)->orderBy('created_at', 'desc')->get();
        
        $penjualan = $penjualan->toArray();

        $data = [];
        $i    = $offset;
        foreach ($penjualan as $key => $value) {
            $data[] = [
                'number'             => ++$i,
                'no'            => $value['no_transaksi'],
                'customer'           => isset($value['customer']['nama']) ? $value['customer']['nama'] : '-',
                'dokter'           => isset($value['dokter']['nama']) ? $value['dokter']['nama'] : '-',
                'jumlah'            => count($value['transaksi']),
                'jenis'         => $value['jenis'],
                'tanggal'     => date('d M Y H:i',strtotime($value['tanggal'])),
                'total'         => 'Rp.'. number_format($value['total'],0,'.','.'),
                'diskon'         => $value['diskon'].'%',
                'harga'         => 'Rp.'. number_format($value['total_harga'],0,'.','.'),
                'action'    => '<button data-id="'.$value['id'].'" type="button" class="btn btn-sm btn-info get-detail"><i class="fa fa-eye">Detail</i></button>'
            ];
        }

        $column = array(
            array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
            array('header' => 'No Transaksi', 'data' => 'no', 'width' => '250px'),
            array('header' => 'Tanggal Penjualan', 'data' => 'tanggal', 'width' => '250px'),
            array('header' => 'Jumlah Transaksi', 'data' => 'jumlah', 'width' => '250px'),
            array('header' => 'Total', 'data' => 'total', 'width' => '250px'),
            array('header' => 'Diskon', 'data' => 'diskon', 'width' => '250px'),
            array('header' => 'Total Harga', 'data' => 'harga', 'width' => '250px'),
            array('header' => 'Pelanggan', 'data' => 'customer', 'width' => '250px'),
            array('header' => 'Dokter', 'data' => 'dokter', 'width' => '250px'),
            array('header' => 'Jenis Transaksi', 'data' => 'jenis', 'width' => '250px'),
            array('header' => 'Detail', 'data' => 'action', 'width' => '100px'),
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
            'title'              => 'Penjualan',
            'breadcrumb'         => [
                ['url' => url('/'), 'text' => '<i class="fa fa-dashboard"></i> Dashboard'],
                ['url' => '#', 'text' => '<i class="fa fa-tag"></i> Report Penjualan'],
            ],
            'header_title'       => 'Report',
            'header_description' => 'Penjualan',
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
        ];
        
        return view("report/penjualan", $data);

    }

    function remote()
    {
        if (isPost() && isAjax()) {
            switch (post('action')) {
                case 'get-transaction':
                    $transaksi = TransaksiPenjualan::where('status','!=',9)->where('id_penjualan',post('id'));

                    $transaksi = $transaksi->with('obat')->get()->toArray();
                    
                    $data = [];
                    $i    = 0;
                    foreach ($transaksi as $key => $value) {

                        $kategori = Kategori::where('id',$value['obat']['kategori'])->first();
                        $data[] = [
                            'number'             => ++$i,
                            'kode'               => $value['obat']['kode'],
                            'nama'               => $value['obat']['nama'],
                            'kategori'           => isset($kategori->nama) ? $kategori->nama : '-',
                            'harga_satuan'  => 'Rp.'. number_format($value['total'],0,'.','.'),
                            'satuan'  => $value['obat']['satuan'],
                            'type'               => $value['obat']['type'] == 1 ? 'Sendiri' : 'Konsinyasi',
                            'jumlah' => $value['jumlah'],
                            'total' => 'Rp.'. number_format($value['total_harga'],0,'.','.'),
                            'jual_pack'             => $value['jual_pack'] ? 'Ya' : 'Tidak'
                        ];
                    }

                    $column = array(
                        array('header' => 'No', 'data' => 'number', 'width' => '30px', 'class' => 'text-center'),
                        array('header' => 'Kode Obat', 'data' => 'kode', 'width' => '250px'),
                        array('header' => 'Nama Obat', 'data' => 'nama', 'width' => '250px'),
                        array('header' => 'Kategori', 'data' => 'kategori', 'width' => '250px'),
                        array('header' => 'Satuan', 'data' => 'satuan', 'width' => '250px'),
                        array('header' => 'Status', 'data' => 'type', 'width' => '250px'),
                        array('header' => 'Harga Satuan', 'data' => 'harga_satuan', 'width' => '250px'),
                        array('header' => 'Jumlah', 'data' => 'jumlah', 'width' => '250px'),
                        array('header' => 'Total', 'data' => 'total', 'width' => '250px'),
                        array('header' => 'Jual Pack', 'data' => 'jual_pack', 'width' => '250px'),
                    );

                    $table = $this->table->create_list(['class' => 'table'], $data, $column);
                    
                    return $table;

                    break;
                default:
                    break;
            }
        }
    }

    /**
     * build search url then redirect
     * @return redirect
     */
    function search()
    {
        if (isPost()) {
            $param     = [];
            $paramable = ['start','end','no_transaksi'];
            foreach ($paramable as $key => $value) {
                $post = post($value);
                if ($post!='')
                    $param[$value] = $post;
            }
            return redirect()->route($this->_route, $param);
        }
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

        $report_title = 'Report Data Penjualan '. $_title;
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcelces();
        // Set properties
        $objPHPExcel->getProperties()->setCreator("www.elysian.web.id");
        $objPHPExcel->getProperties()->setLastModifiedBy("www.elysian.web.id");
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
        
        $objPHPExcel->getActiveSheet()->setTitle('Report Data Penjualan '. $_title);

        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'No.');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'No Transaksi');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Tanggal penjualan');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Jumlah Transaksi');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Total');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Diskon');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Total Harga');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Jenis');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Pelanggan');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Dokter');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;

        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'DETAIL => ');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;

        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Kode Obat');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Nama Obat');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Kategori');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Satuan');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Status');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Harga Satuan');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Jumlah');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Total');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;
        $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Jual Pack');
        $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($styleHeader);
        $abj++;

        $i++;
        
        foreach ($data as $key => $value) 
        {     
            $abj = 'A';
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $key+1);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;

            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['no_transaksi']);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, date('d M Y H:i',strtotime($value['tanggal'])));
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, count($value['transaksi']));
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Rp.'. number_format($value['total'],0,'.','.'));
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['diskon']);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Rp.'. number_format($value['total_harga'],0,'.','.'));
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $value['jenis']);
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, isset($value['customer']['nama']) ? $value['customer']['nama'] : '-');
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, isset($value['dokter']['nama']) ? $value['dokter']['nama'] : '-');
            $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
            $abj++;
            $abj++;
            foreach ($value['transaksi'] as $k => $v) 
            {   
                $abj = 'L';
                $kategori = Kategori::where('id',$v['obat']['kategori'])->first();
                $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $v['obat']['kode']);
                $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
                $abj++;
                $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $v['obat']['nama']);
                $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
                $abj++;
                $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, isset($kategori->nama) ? $kategori->nama : '-');
                $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
                $abj++;
                $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $v['obat']['satuan']);
                $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
                $abj++;
                $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $v['obat']['type'] == 1 ? 'Sendiri' : 'Konsinyasi');
                $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
                $abj++;
                $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Rp.'. number_format($v['total'],0,'.','.'));
                $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
                $abj++;
                $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $v['jumlah']);
                $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
                $abj++;
                
                $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, 'Rp.'. number_format($v['total_harga'],0,'.','.'));
                $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
                $abj++;
                $objPHPExcel->getActiveSheet()->SetCellValue($abj.$i, $v['jual_pack'] ? 'Ya' : 'Tidak');
                $objPHPExcel->getActiveSheet()->getStyle($abj.$i)->applyFromArray($style);
                $abj++;

                $i++;
            }
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);


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
