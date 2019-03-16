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
                    <div class="col-lg-6 col-md-6 col-sm-12">

                        <div class="form-group">
                            <label for="name" class="col-sm-3 control-label">Nama Biaya</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" id="name" class="form-control input-sm" value="{{ $search['name'] }}" placeholder="Nama Kategori">
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12">

                        <div class="pull-right text-center">
                            <button type="submit" class="btn btn-sm btn-primary btn-flat" id="button-search"><i class="fa fa-search"></i> Search</button>
                            <a href="<?=url($route)?>" class="btn btn-default btn-sm"><i class="fa fa-list-alt"></i> Show All</a>


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
<script src="{{ asset('js/transaksi/setting_biaya.js') }}"></script>
@stop