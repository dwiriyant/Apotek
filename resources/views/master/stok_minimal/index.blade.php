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
<script src="{{ asset('js/master/stok_minimal.js') }}"></script>
@stop