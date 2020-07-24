@extends('layouts.app', ['breadcrumb' => $breadcrumb,'header_title' => $header_title,'header_description' => $header_description])

@section('content')
{!! $flash_message !!}
<style type="text/css">
    .title-max {
        max-width: 600px;word-wrap: break-word;
    }
</style>
<script type="text/javascript">
    var base_url = "{{url('/')}}/";
    var jenis = '{{$jenis}}';
</script>

<div class="box box-success ">
    <div class="box-header no-shadow no-padding nav-tabs-custom" style="margin-bottom: 0px; min-height: 45px;">
    	<h3 class="box-title" style="padding:10px;">Transaksi penjualan obat {{$jenis}}</h3>
    </div>
    <div class="box-body">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="nomor-transaksi" class="control-label">Nomor Transaksi</label>
                <div>
                    <input type="text" name="nomor-transaksi" id="nomor-transaksi" class="form-control" value="<?=$nomor_transaksi?>" placeholder="Nomor Transaksi" disabled>
                </div>
            </div>
            <div class="form-group">
                <input type="hidden" id="tgl_transaksi" name="tgl_transaksi" class="dt-value" value="<?= date('Y-m-d H:i:s',strtotime('now')) ;?>">
                <label for="tgl_transaksi" class="control-label">Tanggal Transaksi</label>
                
                <div class="input-group date2">
                    <input type="text" autocomplete="off" class="form-control" placeholder="Schedule" value="<?= date('d M Y H:i',  strtotime('now'))?>" disabled>
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            @if($jenis=='resep')
            <div class="form-group">
                <label for="dokter" class="control-label">Dokter</label>
                <div>
                    <select class="form-control" id="dokter">
                        @foreach($dokter as $dok)
                        <option value="<?= $dok['id'] ?>"><?= $dok['nama']?></option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

        </div>
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="customer" class="control-label">Customer</label>
                <div>
                    <select class="form-control" id="customer">
                        <option value="" >Langsung</option>
                        @foreach($customer as $cuss)
                        <option value="<?= $cuss['id'] ?>"><?= $cuss['nama']?></option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <p style="font-size: 50px;padding: 30px;" class="money">Rp. <span id="total-atas">0</span></p>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-xs-12 col-md-12">
        <div class="box box-primary">
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:30px;">No</th>
                                <th style="width:250px;">Kode</th>
                                <th style="width:250px;">Nama Obat</th>
                                <th style="width:250px;">Kategori</th>
                                <th style="width:120px;">Satuan</th>
                                <th style="width:120px;">Status</th>
                                <th style="width:200px;">Harga Satuan</th>
                                <th style="width:80px;">Jumlah</th>
                                <th style="width:120px;">Diskon</th>
                                <th style="width:300px;">Total</th>
                                {!!$jenis == 'resep' ? '' : '<th style="width:100px;">Jual Pack?</th>'!!}
                                <th style="width:50px;">X</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="data-kosong">
                                <td colspan="15">Data Kosong !</td>
                            </tr>
                        </tbody>
                        <tbody id="data-obat"></tbody>
                    </table>                
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Cari Kode Obat</label>
                        <div class="input-group">
                            <div id="button-popup" class="input-group-addon">
                                <span style="cursor:pointer;" ><i class="fa fa-folder-open"></i></span>
                            </div>
                            <input id="kode-obat" type="number" autocomplete="off" class="form-control" placeholder="Masukkan Kode Obat" autofocus>
                            <span id="cari-obat" style="cursor:pointer;" class="input-group-addon"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                </div>  
                <div class="col-md-7 col-md-offset-1">
                    <div class="form-group col-md-12 col-xs-6" style="display: inline-block;">
                            <label style="margin-top: 5px;font-size: 20px;" for="name" class="col-sm-3 control-label">Total</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp. </span><input style="height: 40px;font-size: 20px;" type="text" id="total" disabled class="form-control input-sm currency2" value="" placeholder="Harga Sebelum Diskon">
                                </div>
                            </div>
                        </div>
                    <div class="form-group col-md-12 col-xs-6" style="display: inline-block;">
                        <label style="margin-top: 5px;font-size: 20px;" for="name" class="col-sm-3 control-label">Diskon</label>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input style="height: 40px;font-size: 20px;text-align: right;" type="number" id="diskon" class="form-control input-sm" value="" placeholder="0"><span class="input-group-addon"> %</span>
                            </div>
                        </div>
                    </div>
                    @if($jenis=='resep')
                    <div class="form-group col-md-12 col-xs-6" style="display: inline-block;">
                        <label style="margin-top: 5px;font-size: 20px;" for="name" class="col-sm-3 control-label">Jasa Resep</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon">Rp. </span>
                                <input style="height: 40px;font-size: 20px;" type="text" id="jasa-resep" class="form-control input-sm currency2" value="" placeholder="Biaya Jasa Resep">
                            </div>
                        </div>
                    </div>
                    @else
                        <input type="hidden" id="jasa-resep" value="0">
                    @endif
                    <div class="form-group col-md-12 col-xs-6" style="display: inline-block;">
                        <label style="margin-top: 5px;font-size: 20px;" for="name" class="col-sm-3 control-label">Total Harga</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon">Rp. </span><input style="height: 40px;font-size: 20px;color: green;" type="text" id="total-harga" disabled class="form-control input-sm currency2" value="" placeholder="Total Harga Setelah Diskon">
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12 col-xs-6" style="display: inline-block;">
                        <label style="margin-top: 5px;font-size: 20px;" for="name" class="col-sm-3 control-label">Uang</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon">Rp. </span>
                                <input style="height: 40px;font-size: 20px;" type="text" id="uang" class="form-control input-sm currency2" value="" placeholder="Jumlah Uang Pelaggan">
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12 col-xs-6" style="display: inline-block;">
                        <label style="margin-top: 5px;font-size: 17px;" for="name" class="col-sm-3 control-label">Uang Kembali</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon">Rp. </span>
                                <input style="height: 40px;font-size: 20px;color: red;" type="text" id="uang-kembali" class="form-control input-sm" value="" disabled placeholder="Kembali">
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12 col-xs-6 col-md-offset-3" style="display: inline-block;">
                        <div class="pull-left text-center">
                            <button type="submit" class="btn btn-primary" id="simpan" disabled><i class="fa fa-save"></i> Simpan</button>
                            <button type="button" class="btn btn-success" id="simpan-cetak" disabled><i class="fa fa-save"></i> Simpan & Cetak</button>
                        </div>
                    </div>  
                </div>  
            </div>
        </div>
    </div>
</div>

<style>
    @media screen and (min-width: 768px) {
    .modals-class {
        width: 70%; /* either % (e.g. 60%) or px (400px) */
    }
}
</style>
<!-- Modal -->
<div id="popup-obat" class="modal fade" role="dialog">
  <div class="modal-dialog modals-class">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Data Barang</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">

                        <div class="box-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="obat-keyword" class="form-control pull-right" placeholder="Cari Nama / Kode Obat">

                            <div class="input-group-btn">
                                <button id="obat-search" type="button" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <div id="table-obat"></div>
                    </div>
            
                </div>
            </div>
            
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

@endsection
@section('js')
<script src="{{ asset('js/transaksi/penjualan-reguler.js') }}"></script>
@stop