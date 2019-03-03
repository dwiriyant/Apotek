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
    var kategori = {!! $kategori !!};
</script>
<div class="box box-success ">
    <div class="box-header no-shadow no-padding nav-tabs-custom" style="margin-bottom: 0px; min-height: 45px;">
    	<h3 class="box-title" style="padding:10px;">Transaksi pembelian obat langsung</h3>
    </div>
    <div class="box-body">
        <div class="col-xs-12 col-md-6">
            <input type="hidden" id="jenis" value="<?=$jenis?>">
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
                    <input type="text" autocomplete="off" class="form-control" placeholder="Schedule" value="<?= date('d M Y H:i',  strtotime('now'))?>">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

        </div>
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="supplier" class="control-label">Supplier</label>
                <div>
                    <select class="form-control" id="supplier">
                        <option value="" >Langsung</option>
                        @foreach($supplier as $supp)
                        <option value="<?= $supp['id'] ?>"><?= $supp['nama']?></option>
                        @endforeach
                    </select>
                </div>
            </div>
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
                                <th style="width:250px;">Harga Beli</th>
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
                        <label class="control-label">Cari Kode Obat</label>
                        <div class="input-group">
                            <input id="kode-obat" type="number" autocomplete="off" class="form-control" placeholder="Masukkan Kode Obat" autofocus>
                            <span id="cari-obat" style="cursor:pointer;" class="input-group-addon"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                </div>  
                <div class="col-md-7 col-md-offset-1">
                    <div class="form-group col-md-12 col-xs-6" style="display: inline-block;">
                        <label style="margin-top: 5px;font-size: 20px;" for="name" class="col-sm-3 control-label">Total Harga</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon">Rp. </span><input style="height: 40px;font-size: 20px;" type="text" id="total" readonly class="form-control input-sm currency2" value="" placeholder="Total Harga">
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12 col-xs-6 col-md-offset-3" style="display: inline-block;">
                        <div class="pull-left text-center">
                            <button type="submit" class="btn btn-primary" id="simpan" disabled><i class="fa fa-save"></i> Simpan</button>
                        </div>
                    </div>  
                </div>  
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
<script src="{{ asset('js/transaksi/pembelian-reguler.js') }}"></script>
@stop