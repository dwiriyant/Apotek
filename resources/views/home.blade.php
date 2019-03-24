@extends('layouts.app', ['breadcrumb' => $breadcrumb,'header_title' => $header_title,'header_description' => $header_description])

@section('content')
<script type="text/javascript">
    var base_url = "{{url('/')}}/";
</script>
<div class="row">
    
    <div class="col-lg-6 col-xs-12" id="step2">
        <div class="callout callout-info" style="min-height: 128px;">
            <h4>Shortcut Button!</h4>

            <p>Click button below for quick access</p>

            <p>
            <a href="<?php echo route('penjualan-reguler') ?>" class="btn bg-navy" ><i
                    class="fa fa-shopping-cart"></i> Penjualan</a>
        
            <a href="<?php echo route('pembelian-reguler') ?>" class="btn btn-warning" ><i class="fa fa-shopping-cart"></i>
                Pembelian</a>
            <a href="<?php echo route('dashboard-po') ?>" class="btn btn-primary" ><i class="fa fa-shopping-cart"></i>
                Pembelian PO</a>
                
            </p>
        </div>
    </div>
    <div class="col-lg-12 col-xs-12">

        <div class="col-xs-12 col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Stok Minimal</h3>
                    <div class="box-tools pull-right">
                    </div>
                </div>
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
</div>
@endsection