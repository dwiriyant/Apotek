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
<div class="row">
    <div class="col-xs-12 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-search"></i> Search</h3>
            </div>
            <div class="box-body">
                <form id="form_edit_id" action="<?php echo url($route.'/search') ?>" method="post" class="form-horizontal">
                    {{ csrf_field() }}
                    <div class="col-lg-4 col-md-6 col-sm-12">

                        <div class="form-group">
                            <label for="name" class="col-sm-3 control-label">Kode Obat</label>
                            <div class="col-sm-9">
                                <input type="text" name="kode" id="kode" class="form-control input-sm" value="{{ $search['kode'] }}" placeholder="Kode Obat">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="kategori" class="col-sm-3 control-label">Satuan Obat</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="satuan">
                                    <option value="" >All</option>
                                    <option value="tablet" <?=@$obat['satuan']=='tablet' ? 'selected' : ''?> >Tablet</option>
                                    <option value="kapsul" <?=@$obat['satuan']=='kapsul' ? 'selected' : ''?> >Kapsul</option>
                                    <option value="botol" <?=@$obat['satuan']=='botol' ? 'selected' : ''?> >Botol</option>
                                    <option value="kotak" <?=@$obat['satuan']=='kotak' ? 'selected' : ''?> >Kotak</option>
                                    <option value="ml" <?=@$obat['satuan']=='ml' ? 'selected' : ''?> >ML</option>
                                    <option value="vial" <?=@$obat['satuan']=='vial' ? 'selected' : ''?> >Vial</option>
                                    <option value="tube" <?=@$obat['satuan']=='tube' ? 'selected' : ''?> >Tube</option>
                                    <option value="pot" <?=@$obat['satuan']=='pot' ? 'selected' : ''?> >Pot</option>
                                    <option value="supp" <?=@$obat['satuan']=='supp' ? 'selected' : ''?> >Supp</option>
                                    <option value="ampul" <?=@$obat['satuan']=='ampul' ? 'selected' : ''?> >Ampul</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        
                        <div class="form-group">
                            <label for="name" class="col-sm-3 control-label">Nama Obat</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" id="name" class="form-control input-sm" value="{{ $search['name'] }}" placeholder="Nama Obat">
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="kategori" class="col-sm-3 control-label">Kategori Obat</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="kategori">
                                    <option value="" >All</option>
                                    @foreach($kategori as $kat)
                                    <option value="<?= $kat['id'] ?>" <?=@$search['kategori']=='1' ? 'selected' : ''?> ><?= $kat['nama']?></option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="pull-right text-center">
                            <button type="submit" class="btn btn-sm btn-primary btn-flat" id="button-search"><i class="fa fa-search"></i> Search</button>
                            <a href="<?=url($route)?>" class="btn btn-default btn-sm"><i class="fa fa-list-alt"></i> Show All</a>
                            <a href="<?=route('report-stok-opname',$param)?>&export=true" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-save"></i> Export</a>
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-xs-12 col-md-12">
        <div class="box box-primary">
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <?php echo $table; ?>
                </div>
            </div>
        </div>
        <div class="clearfix" style="margin-bottom:20px;">
            <div class="pull-left">
                Showing <strong><?php echo $offset + 1; ?></strong> - <strong><?php echo $offset+$limit > $total ? $total : $offset+$limit ;?></strong> of <strong><?php echo $total?></strong> data
            </div>
            <?php echo $pagination; ?>
        </div>
    </div>

    
</div>


@endsection
@section('js')
<script src="{{ asset('js/report/stok_opname.js') }}"></script>
@stop