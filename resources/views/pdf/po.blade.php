<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PO</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <style type="text/css">
        @page {
            margin:0px 15px 0px 15px;
        }

        body {
            margin:0px 15px 0px 15px;
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
        .information .logo {
            margin: 5px;
        }

        h1,h2 {
            margin: 2px;
        }

        h3,.m0{
            margin: 0px;
        }

        .information table {
            padding: 5px;
        }

        .fr {
            float: right;
        }

        .tables {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;

        }

        .tables td, .tables th {
            border: 1px solid #000;
            text-align: left;
            padding: 2px;
            margin: 0px;
            font-weight: bold;
        }
        .tables h3 {
            margin-left: 5px;
        }

        .tables th {
            text-align: center;
        }
    </style>


</head>

<body>

    <div class="information">
        <table border="0" cellpadding="0" cellspacing="0" style="width:25%;float:left">
            <tbody>
                <tr>
                    <td>
                        
                        <img style="float:right;width:55%;margin-top: 10px;" src="{{ asset('img/logo.png') }}">
                    </td>
                </tr>
            </tbody>
        </table>
        <table border="0" cellpadding="0" cellspacing="0" style="width:75%;float:left;margin-top: 10px;margin-left: -41px;">
            <tbody>
                <tr>
                    <td align="center" style="font-weight: bold;">
                        <h1>{{ ucwords(getToko('nama')) }}</h1>
                        <h3>{{ getToko('alamat') }}</h3>
                        <h3>{{ getToko('no_telp') }}</h3>
                    </td>
                </tr>
            </tbody>
        </table>

        <hr style="border-left:0px;border-right:0px;border-top: 0px;border-bottom:5px solid black;padding-top:10px; margin:25px auto 0px auto;clear:both" />

        

        <table width="100%;">
            <tr>
                <td align="center">
                    <h1 class="m0">SURAT PESANAN</h1>
                </td>
            </tr>
        </table>

        <table border="0" cellpadding="0" cellspacing="0" style="width:30%;float:left">
            <tbody>
                <tr>
                    <td>
                        <h2>No Transaksi</h2>
                        <h2>Kepada PBF</h2>
                        <h2>Tanggal</h2>
                    </td>
                </tr>
            </tbody>
        </table>
        <table border="0" cellpadding="0" cellspacing="0" style="width:70%;float:left;">
            <tbody>
                <tr>
                    <td>
                        <h2>: {{ $penjualan['no_transaksi']}}</h2>
                        <h2>: {{ $penjualan['supplier']['nama']}}</h2>
                        <h2>: {{ date('d / m / Y', strtotime($penjualan['tanggal'])) }}</h2>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="tables" style="width:100%;">
            <tr>
                <th style="width:10%;">
                    <h2>No</h2>
                </th>
                <th style="width:70%;">
                    <h2>Nama Barang</h2>
                </th>
                <th style="width:20%;">
                    <h2>Jumlah</h2>
                </th>
            </tr>
            
            @foreach ($transaksi as $key => $item)
                <tr>
                    <td style="text-align: center"><h3>{{$key+1}}</h3></td>
                    <td><h3>{{$item['obat_po']['nama']}}</h3></td>
                    <td><h3>{{$item['jumlah']}}</h3></td>
                </tr>
            @endforeach                
            
        </table>

        <table border="0" cellpadding="0" cellspacing="0" style="width:70%;float:left">
            <tr>
                <td align="center">
                </td>
            </tr>
        </table>

        <table border="0" cellpadding="0" cellspacing="0" style="width:30%;float:left;margin-top: 10px;">
            <tr>
                <td align="center">
                    <h2 class="ttd">Apoteker</h2>
                </td>
            </tr>
        </table>

        <table border="0" cellpadding="0" cellspacing="0" style="width:70%;float:left">
            <tr>
                <td align="center">
                </td>
            </tr>
        </table>

        <table border="0" cellpadding="0" cellspacing="0" style="width:29%;float:left;margin-top: 50px;margin-left: 5px;">
            <tr>
                <td align="center">
                    <h2 class="ttd-nama">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</h2>
                </td>
            </tr>
        </table>

    </div>

<script type="text/javascript"> 
    this.print(true) 
</script> 
</body>

</html>