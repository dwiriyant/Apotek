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
                            <label for="no_transaksi" class="col-sm-3 control-label">NoTransaksi</label>
                            <div class="col-sm-9">
                                <input type="text" name="no_transaksi" id="no_transaksi" class="form-control input-sm" value="{{ $search['no_transaksi'] }}" placeholder="No Transaksi">
                            </div>
                        </div> 

                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        
                        <div class="form-group">
                            <label for="datetimepicker1" class="control-label col-sm-3">Tanggal</label>
                            <div class="col-sm-9">
                                <div class="input-daterange" id="datepicker">
                                    <div class="input-group">
                                        <input type="hidden" name="start" value="<?=$search['start']?>">
                                        <input type="text" id="datetimepicker1" class="input-sm form-control date" rel="start" value="<?=$search['start'] ? date('d M Y', strtotime($search['start'])) : '' ?>">
                                        <span class="input-group-addon">to</span>
                                        <input type="hidden" name="end" value="<?=$search['end']?>">
                                        <input type="text" id="datetimepicker2" class="input-sm form-control date" rel="end" value="<?=$search['end'] ? date('d M Y', strtotime($search['end'])) : '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">

                        <div class="pull-right text-center">
                            <button type="submit" class="btn btn-sm btn-primary btn-flat" id="button-search"><i class="fa fa-search"></i> Search</button>
                            <a href="<?=url($route)?>" class="btn btn-default btn-sm"><i class="fa fa-list-alt"></i> Show All</a>
                            <a href="<?=route('report-penjualan',$param)?>&export=true" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-save"></i> Export</a>


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

<style>
    @media screen and (min-width: 768px) {
    .modals-class {
        width: 70%; /* either % (e.g. 60%) or px (400px) */
    }
}
</style>
<!-- Modal -->
<div id="popup-detail" class="modal fade" role="dialog">
  <div class="modal-dialog modals-class">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Detail Transaksi</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body table-responsive no-padding">
                        <div id="table-detail"></div>
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
<script src="{{ asset('js/report/penjualan.js') }}"></script>
@stop