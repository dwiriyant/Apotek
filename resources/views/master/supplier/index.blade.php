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
                            <label for="name" class="col-sm-3 control-label">Nama</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" id="name" class="form-control input-sm" value="{{ $search['name'] }}" placeholder="Nama Supplier">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="telepon" class="col-sm-3 control-label">Telepon</label>
                            <div class="col-sm-9">
                                <input type="text" name="telepon" id="telepon" class="form-control input-sm" value="{{ $search['telepon'] }}" placeholder="Telepon Supplier">
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        
                        <div class="form-group">
                            <label for="alamat" class="col-sm-3 control-label">Alamat</label>
                            <div class="col-sm-9">
                                <input type="text" name="alamat" id="alamat" class="form-control input-sm" value="{{ $search['alamat'] }}" placeholder="Alamat Supplier">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="kota" class="col-sm-3 control-label">Kota</label>
                            <div class="col-sm-9">
                                <input type="text" name="kota" id="kota" class="form-control input-sm" value="{{ $search['kota'] }}" placeholder="Kota Supplier">
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        
                        <div class="form-group">
                            <label for="no_rek" class="col-sm-3 control-label">No Rek</label>
                            <div class="col-sm-9">
                                <input type="text" name="no_rek" id="no_rek" class="form-control input-sm" value="{{ $search['no_rek'] }}" placeholder="No Rekening">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-9">
                                <input type="text" name="email" id="email" class="form-control input-sm" value="{{ $search['email'] }}" placeholder="Email Supplier">
                            </div>
                        </div>

                        <div class="pull-right text-center">
                            <button type="submit" class="btn btn-sm btn-primary btn-flat" id="button-search"><i class="fa fa-search"></i> Search</button>
                            <a href="<?=url($route)?>" class="btn btn-default btn-sm"><i class="fa fa-list-alt"></i> Show All</a>
                            <a href="<?=route('supplier',$param)?>&export=true" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-save"></i> Export</a>


                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-md-12" id="form-container">
        <?php echo $form?>
    </div>

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
<script src="{{ asset('js/master/supplier.js') }}"></script>
@stop