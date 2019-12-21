<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Transaksi</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <style type="text/css">
        @page {
            margin: 0px;
        }

        body {
            margin: 0px;
        }

        * {
            /* font-family: Verdana, Arial, sans-serif; */
            font-family: 'Open Sans', sans-serif;
        }

        a {
            color: #000;
            text-decoration: none;
        }

        table {
            font-size: x-small;
        }

        h3 {
            margin: 0px;
        }

        .information table {
            padding: 5px;
        }

        .fr {
            float: right;
        }

    </style>


</head>

<body>

<div class="information">
    <table width="100%">
        <tr>
            <td align="center" >
                <h3>{{ $toko['nama'] }}</h3>
                <h3>{{ $toko['alamat'] }}</h3>
                <h3>{{ $toko['no_telp'] }}</h3>

            </td>
        </tr>

    </table>

    <table border="0" cellpadding="0" cellspacing="0" style="width:30%;float:left">
        <tbody>
            <tr>
                <td>
                    <h3>TANGGAL</h3>
                    <h3>Transaksi</h3>
                    <h3>Jenis</h3>
                    <h3>Nama Plg</h3>
                    {{ $data['penjualan']['jenis'] == 'resep' ? '<h3>Nama Dokter</h3>' : '' }}
                </td>
            </tr>
        </tbody>
    </table>
    <table border="0" cellpadding="0" cellspacing="0" style="width:70%;float:left">
        <tbody>
            <tr>
                <td>
                    <h3>: {{ date('d / m / Y', strtotime($data['penjualan']['tanggal'])) }}</h3>
                    <h3>: {{ $data['penjualan']['no_transaksi'] }}</h3>
                    <h3>: {{ $data['penjualan']['jenis'] }}</h3>
                    <h3>: {{ @$data['penjualan']['customer']['nama'] }}</h3>
                    {{ $data['penjualan']['jenis'] == 'resep' ? '<h3>: '.@$data['penjualan']['dokter']['nama']. '</h3>' : '' }}
                </td>
            </tr>
        </tbody>
    </table>

    <table width="100%;" style="margin-left: -5px;">
        <tr>
            <td align="center">
                <h3>====================================================================================</h3>
            </td>
        </tr>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" style="width:100%;float:left;">
        <thead>
            <tr>
            <td style="width:70%">&nbsp;</td>
            <td style="width:30%">&nbsp;</td>
            </tr>
        </thead>
        <tbody>
            
                
            @foreach ($data['transaksi'] as $transaksi)
                <tr>
                    <td>
                        <h3>{{$transaksi['obat']['nama']}}</h3>
                    </td>
                    <td class="fr">
                        <h3>{{$transaksi['obat']['satuan']}}</h3>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h3 style="margin-left: 7px;">{{$transaksi['jumlah']}} x {{number_format($transaksi['total'],2,",",".")}}</h3>
                    </td>
                    <td class="fr">
                        <h3 style="text-align: right;">{{number_format($transaksi['total_harga'],2,",",".")}}</h3>
                    </td>
                </tr>

                        
            @endforeach
                
            
        </tbody>
    </table>

    <table width="100%;" style="margin-left: -10px;">
        <tr>
            <td align="center">
                <h3>====================================================================================</h3>
            </td>
        </tr>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" style="width:40%;float:left;">
        <tbody>
            <tr>
                <td>
                    <h3 style="margin-left: 10px;">{{ count($data['transaksi']) }} Item</h3>
                </td>
            </tr>
        </tbody>
    </table>
    
    <table border="0" cellpadding="0" cellspacing="0" style="width:28%;float:left">
        <tbody>
            <tr>
                <td>
                    <h3>Sub Total </h3>
                    <h3>Diskon </h3>
                    <h3>Total </h3>
                    <h3>Bayar </h3>
                    <h3>Kembali </h3>
                </td>
            </tr>
        </tbody>
    </table>
    <table border="0" cellpadding="0" cellspacing="0" style="width:2%;float:left">
        <tbody>
            <tr>
                <td>
                    <h3> :</h3>
                    <h3> :</h3>
                    <h3> :</h3>
                    <h3> :</h3>
                    <h3> :</h3>
                </td>
            </tr>
        </tbody>
    </table>
    <table border="0" cellpadding="0" cellspacing="0" style="width:30%;float:right">
        <tbody>
            <tr>
                <td class="fr" style="float: right;">
                    <div style="text-align: right;">
                    <h3>{{ number_format($data['penjualan']['total'],2,",",".") }}</h3>
                    <h3>{{ number_format(($data['penjualan']['total'] * $data['penjualan']['diskon'] / 100),2,",",".") }}</h3>
                    <h3>{{ number_format($data['penjualan']['total_harga'],2,",",".") }}</h3>
                    <h3>{{ number_format($data['penjualan']['uang'],2,",",".") }}</h3>
                    <h3>{{ number_format(($data['penjualan']['uang'] - $data['penjualan']['total_harga']),2,",",".") }}</h3>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <table width="100%;" style="margin-left: -10px;">
        <tr>
            <td align="center">
                <h3>====================================================================================</h3>
            </td>
        </tr>
    </table>

    <table width="100%;">
        <tr>
            <td align="center">
                <h3>Opr : {{ Auth::user()->name }} - {{ date('d / m / Y H:i:s', strtotime($data['penjualan']['tanggal'])) }}</h3>
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td align="center">
                <h3>Terimakasih Atas Kunjungannya</h3>
                <h3>Semoga Lekas Sembuh</h3>
                <h3>---------------------------------</h3>
    
            </td>
        </tr>
    
    </table>

</div>
<script type="text/javascript"> 
    this.print(true) 
</script> 
</body>

</html>