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
                            <label for="name" class="col-sm-3 control-label">Campaign name</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" id="name" class="form-control input-sm" value="{{ $search['name'] }}" placeholder="Campaign name">
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        
                        <div class="form-group">
                            <label for="input_start" class="col-sm-3 control-label" style="    padding-top: 0px;">Campaign Date</label>
                            <div class="col-sm-9" id="form-search-range">
                                
                                <div class="input-group">
                                    <input type="hidden" name="start" id="input_dStart" class="dt-value" value="<?=$search['start']?>">
                                    <input type="text" placeholder="Start Date" name="input_start" class="form-control input-sm date" id="datetimepicker01" value="<?php echo (isset($search['start']) && $search['start']) ? getFormattedDate($search['start'], 'd M Y', 'Y-m-d') : '' ;?>">
                                    <span class="input-group-addon">to</span>
                                    <input type="hidden" name="end" id="input_dEnd" class="dt-value" value="<?=$search['end']?>">
                                    <input type="text" placeholder="End Date" name="input_end" class="form-control input-sm date" id="datetimepicker02" value="<?php echo (isset($search['end']) && $search['end']) ? getFormattedDate($search['end'], 'd M Y', 'Y-m-d') : '' ;?>">
                                </div>
                                
                            </div>
                        </div>

                        

                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="status" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status">
                                    <option value="" <?=@$search['status']=='' ? 'selected': ""?> >All</option>
                                    <option value="NY" <?=@$search['status']=='NY' ? 'selected': ""?> >Not Yet</option>
                                    <option value="OG" <?=@$search['status']=='OG' ? 'selected': ""?> >On Going</option>
                                    <option value="D" <?=@$search['status']=='D' ? 'selected': ""?> >Done</option>
                                </select>
                            </div>
                        </div>

                        <div class="pull-right text-center">
                            <button type="submit" class="btn btn-sm btn-primary btn-flat" id="button-search"><i class="fa fa-search"></i> Search</button>
                            <a href="<?=url($route)?>" class="btn btn-default btn-sm"><i class="fa fa-list-alt"></i> Show All</a>
                            <a href="<?=route('obat',$param)?>&export=true" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-save"></i> Export</a>


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
<script src="{{ asset('js/master/obat.js') }}"></script>
@stop