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
</script>

<div class="box box-success ">
    <div class="box-header no-shadow no-padding nav-tabs-custom" style="margin-bottom: 0px; min-height: 45px;">
    	<h3 class="box-title" style="padding:10px;">Transaksi penjualan obat reguler</h3>
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

        </div>
        <div class="col-xs-12 col-md-6">
            <p style="font-size: 50px;padding: 30px;" class="money">Rp. <span id="total-atas"></span></p>
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
                                <th style="width:250px;">Harga Satuan</th>
                                <th style="width:100px;">Jumlah</th>
                                <th style="width:300px;">Total</th>
                                <th style="width:50px;">X</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="data-kosong">
                                <td colspan="9">Data Kosong !</td>
                            </tr>
                        </tbody>
                        <tbody id="data-obat"></tbody>
                    </table>                
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Cari Obat</label>
                        <div class="input-group">
                            <input id="kode-obat" type="text" autocomplete="off" class="form-control" placeholder="Masukkan Kode Obat" autofocus>
                            <span id="cari-obat" style="cursor:pointer;" class="input-group-addon"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                </div>  
                <div class="col-md-7 col-md-offset-1">
                    <div class="form-group col-md-12 col-xs-6" style="display: inline-block;">
                            <label style="margin-top: 5px;font-size: 20px;" for="name" class="col-sm-3 control-label">Total</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp. </span><input style="height: 40px;font-size: 20px;" type="text" id="total" readonly class="form-control input-sm currency2" value="" placeholder="Harga Sebelum Diskon">
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
                    <div class="form-group col-md-12 col-xs-6" style="display: inline-block;">
                        <label style="margin-top: 5px;font-size: 20px;" for="name" class="col-sm-3 control-label">Total Harga</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon">Rp. </span><input style="height: 40px;font-size: 20px;color: green;" type="text" id="total-harga" readonly class="form-control input-sm currency2" value="" placeholder="Total Harga Setelah Diskon">
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
                                <input style="height: 40px;font-size: 20px;color: red;" type="text" id="uang-kembali" class="form-control input-sm" value="" readonly placeholder="Kembali">
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

@endsection
@section('js')
<script src="{{ asset('js/transaksi/penjualan-reguler.js') }}"></script>
@stop