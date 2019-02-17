@extends('layouts.app', ['breadcrumb' => $breadcrumb,'header_title' => $header_title,'header_description' => $header_description])

@section('content')
<script type="text/javascript">
    var base_url = "{{url('/')}}/";
</script>
<div class="row">
    <div class="col-lg-3 col-xs-6" id="step1">
        <!-- small box -->
        <div class="small-box bg-aqua" id="total-news">
            <div class="inner">
                <h3 id="news-point">125</h3>

                <p>Total Penjualan</p>
            </div>
            <div class="icon">
                <i class="fa fa-newspaper-o"></i>
            </div>
            <a href="<?php echo route('news', ['start' => date('Y-m-d'), 'end' => date('Y-m-d')]) ?>"
               class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6" id="step1">
        <!-- small box -->
        <div class="small-box bg-green" id="total-page-view">
            <div class="inner">
            <h3 id="page-view-point">220</h3>
                <p>Total Pembelian</p>
            </div>
            <div class="icon">
                <i class="fa fa-bar-chart"></i>
            </div>
            <a href="<?php echo route('report-kpi', ['start' => date('Y-m-d'), 'end' => date('Y-m-d')]) ?>"
               class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-6 col-xs-12" id="step2">
        <div class="callout callout-info" style="min-height: 128px;">
            <h4>Shortcut Button!</h4>

            <p>Click button below for quick access</p>

            <p>
            <a href="<?php echo route('/') ?>" class="btn bg-navy" ><i
                    class="fa fa-edit"></i> Penjualan</a>
        
            <a href="<?php echo route('/') ?>" class="btn btn-warning" ><i class="fa fa-edit"></i>
                Pembelian</a>
                
            </p>
        </div>
    </div>
</div>
@endsection