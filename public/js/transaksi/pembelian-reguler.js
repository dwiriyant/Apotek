$(document).scannerDetection({
    timeBeforeScanTest: 200, // wait for the next character for upto 200ms
    avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms

    endChar: [13],
    onComplete: function(barcode, qty){
        $(document).focus();
        cariObat(barcode);
    },
    onError: function(string, qty) {
    }
    
});

$("#button-popup").click(function () {
    cariObatPopup();
    $("#popup-obat").modal('show');
});

$("#obat-search").click(function () {
    cariObatPopup();
});

$('#obat-keyword').keypress(function (e) {
    var key = e.which;
    if (key == 13)  // the enter key code
    {
        cariObatPopup();
        return false;
    }
});

function cariObatPopup() {
    keyword = $("#obat-keyword").val();
    $.ajax({
        url: base_url + 'penjualan-reguler/remote',
        method: 'post',
        data: { action: 'cari-obat-popup', keyword: keyword },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            callLoader();
        }
    }).always(function () {
        endLoader();
    }).done(function (html) {
        $('#table-obat').html(html);
    }).fail(function (jqXHR, textStatus, errorThrown) {
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
}


$('#kode-obat').focus();
$('#simpan').prop('disabled', true);
var d = new Date();
d.setHours(0,0,0,0);
var total_obat = 0;
var total_harga = 0;
var diskon = 0;
var harga_diskon = 0;

function checkButton()
{
    if($('#data-obat').html()=='')
    {
        $('#simpan').prop('disabled', true);
        $('#simpan-cetak').prop('disabled', true);
    } else
    {
        $('#simpan').prop('disabled', false);
        $('#simpan-cetak').prop('disabled', false);
    }
}
function hapusObat(id)
{
    swal({
        title: "hapus data?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        showLoaderOnConfirm: true,
        closeOnConfirm: true
    },function(isConfirm){
        $('#list-'+id).remove();
        reorderNomor();
        if($('#data-obat').html()=='')
        {
            $("#data-kosong").html('<td colspan="9">Data Kosong !</td>');
            $("#data-kosong").show();
            total_obat = 0;
        }
        checkButton();
    });
}

function reorderNomor()
{
    $(".nomor").html('');
    var table = document.getElementsByTagName('table')[0],
        rows = table.getElementsByTagName('tr'),
        text = 'textContent' in document ? 'textContent' : 'innerText';

    for (var i = 1, len = rows.length; i < len; i++){
        rows[i].children[0][text] = i-1 + '.' + rows[i].children[0][text];
    }
}

function checkTotal() {
    
    total_harga = 0;
    for(i=1;i<=total_obat;i++)
    {
        if($("#jumlah-"+i).length > 0)
            int_jumlah_obat = parseInt(getNumber($("#jumlah-"+i).val()));
        else
            int_jumlah_obat = 1;
        if ($("#harga-" + i).length > 0)
        {
            int_total_obat = parseInt(getNumber($("#harga-" + i).val()));
            harga_format = 'Rp. ' +int_total_obat.format();
            $("#harga-" + i).val(harga_format);
        }
        else
            int_total_obat = 0;

        if(isNaN(int_jumlah_obat))
        {
            int_jumlah_obat = 1;
        }

        if (isNaN(int_total_obat)) {
            int_total_obat = 0;
        }
        
        if(int_jumlah_obat <= 0)
        {
            int_jumlah_obat = 1;
            $("#jumlah-"+i).val('1');
        }

        if (int_total_obat < 0) {
            int_total_obat = 0;
            $("#harga-" + i).val('0');
        }
    
        $("#total-" + i).html('Rp. ' + (int_jumlah_obat * int_total_obat).format()); if ($("#jumlah-" + i).length > 0)
            int_jumlah_obat = parseInt(getNumber($("#jumlah-" + i).val()));
        else
            int_jumlah_obat = 1;
        if ($("#harga-" + i).length > 0) {
            int_total_obat = parseInt(getNumber($("#harga-" + i).val()));
            harga_format = 'Rp. ' + int_total_obat.format();
            $("#harga-" + i).val(harga_format);
        }
        else
            int_total_obat = 0;

        if (isNaN(int_jumlah_obat)) {
            int_jumlah_obat = 1;
        }

        if (isNaN(int_total_obat)) {
            int_total_obat = 0;
        }

        if (int_jumlah_obat <= 0) {
            int_jumlah_obat = 1;
            $("#jumlah-" + i).val('1');
        }

        if (int_total_obat < 0) {
            int_total_obat = 0;
            $("#harga-" + i).val('0');
        }

        $("#total-" + i).html('Rp. ' + (int_jumlah_obat * int_total_obat).format());
        if($("#total-"+i).length > 0)
            total_harga += parseInt(getNumber($("#total-"+i).text()));
    }

    $('#total').val(total_harga.format());


    $('.currency2').each(function(){ 
        $(this).maskMoney('mask', $(this).val());
    })

    $('.currency').each(function () {
        $(this).maskMoney('mask', $(this).val());
    })

    checkButton();
}

function cariObat(kode_obat = '') {
    if(kode_obat=='')
        id_obat = $('#kode-obat').val();
    else
        id_obat = kode_obat;

    if(id_obat == '')
        return false;

    $.ajax({
        url : base_url + 'pembelian-reguler/remote',
        method : 'post',
        data: {action:'cari-obat',id:id_obat},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend : function(){
            callLoader();
        }
    }).always(function(){
        endLoader();
    }).done(function(data){
        data = JSON.parse(data);
        data = data.data;
        generateTable(data, id_obat);
        
        $("#popup-obat").modal('hide');
    }).fail(function(jqXHR, textStatus, errorThrown){
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
}

function generateTable(data, id_obat)
{
    if (data != null) {
        callNoty('success', 'Data sudah tersedia.');

        for (i = 1; i <= total_obat; i++) {
            if ($("#total-" + i).length > 0) {
                if ($("#kode-" + i).text() == data.kode) {
                    $("#jumlah-" + i).val(parseInt($("#jumlah-" + i).val()) + 1);
                    checkTotal();
                    return false;
                }
            }
        }
        kat_all = '';
        kategori.forEach(kat => {
            kat_all += '<option value="' + kat.id + '" ' + (data.kategori.id == kat.id ? 'selected' : '') + ' >' + kat.nama + '</option >';
        });
        total_obat++;
        result = '<tr id="list-' + total_obat + '"> ' +
            '<td class="nomor"></td>' +
            '<td><input style="border: 0;" id="kode-' + total_obat + '"  type="number" value="' + id_obat + '" disabled></td>' +
            '<td><input style="border: 0;" id="nama-' + total_obat + '"  type="text" disabled value="' + data.nama + '"></td>' +
            '<td>' +
            '<select class="form-control" id="kategori-' + total_obat + '">' +
            kat_all +
            '</select >' +
            '</td>' +
            '<td><input style="border: 0;" id="satuan-' + total_obat + '"  type="text" disabled value="' + data.satuan + '"></td>' +
            '<td><select class="form-control" id="type-' + total_obat + '"><option value="1"' + (data.type == '1' ? 'selected' : '') + '>Sendiri</option><option value="2"' + (data.type == '2' ? 'selected' : '') + '>Konsinyasi</option></select></td>';
        if (jenis == 'langsung') {

            result +=
                '<td><input style="border: 0;" id="harga-' + total_obat + '" class="currency" type="text" value="0"></td>';
        }
        result +=
            '<td><input style="max-width: 55px;border: 0;" id="jumlah-' + total_obat + '"  type="number" value="1"></td>';
        if (jenis == 'langsung') {
            result +=
                '<td id="total-' + total_obat + '"> Rp. ' + 0 + '</td>';
        }
        result +=
            '<td onclick="hapusObat(' + total_obat + ')" style="cursor:pointer;" data-id="' + total_obat + '"><i style="color:red" class="fa fa-times"></i> </td>' +
            '</tr>';
        $("#data-kosong").hide();
        $("#data-obat").append(result);
        checkTotal();
        reorderNomor();
    }
    else {
        kat_all = '';
        kategori.forEach(kat => {
            kat_all += '<option value="' + kat.id + '" >' + kat.nama + '</option >';
        });
        for (i = 1; i <= total_obat; i++) {
            if ($("#total-" + i).length > 0) {
                if ($("#kode-" + i).val() == id_obat) {
                    $("#jumlah-" + i).val(parseInt($("#jumlah-" + i).val()) + 1);
                    checkTotal();
                    return false;
                }
            }
        }
        callNoty('information', 'Data belum tersedia.');
        total_obat++;

        result = '<tr id="list-' + total_obat + '"> ' +
            '<td class="nomor"></td>' +
            '<td><input style="border: 0;" id="kode-' + total_obat + '"  type="number" value="' + id_obat + '"></td>' +
            '<td><input style="border: 0;" id="nama-' + total_obat + '"  type="text" value=""></td>' +
            '<td>' +
            '<select class="form-control" id="kategori-' + total_obat + '">' +
            kat_all +
            '</select >' +
            '</td>' +
            '<td><select class="form-control" id="satuan-' + total_obat + '"><option value="biji/pc">Biji/pc</option><option value="sachet">Sachet</option><option value="strip">Strip</option><option value="tablet">Tablet</option><option value="kapsul">Kapsul</option><option value="botol">Botol</option><option value="kotak">Kotak</option><option value="ml">ML</option><option value="vial">Vial</option><option value="tube">Tube</option><option value="pot">Pot</option><option value="supp">Supp</option><option value="ampul">Ampul</option></select></td>' +
            '<td><select class="form-control" id="type-' + total_obat + '"><option value="1">Sendiri</option><option value="2">Konsinyasi</option></select></td>';
        if (jenis == 'langsung') {
            result +=
                '<td><input style="border: 0;" id="harga-' + total_obat + '" class="currency" type="text" value="0"></td>';
        }
        result +=
            '<td><input style="max-width: 55px;border: 0;" id="jumlah-' + total_obat + '"  type="number" value="1"></td>';
        if (jenis == 'langsung') {
            result +=
                '<td id="total-' + total_obat + '"> Rp. ' + 0 + '</td>';
        }
        result +=
            '<td onclick="hapusObat(' + total_obat + ')" style="cursor:pointer;" data-id="' + total_obat + '"><i style="color:red" class="fa fa-times"></i> </td>' +
            '</tr>';
        $("#data-kosong").hide();
        $("#data-obat").append(result);
        checkTotal();
        reorderNomor();
    }
}

$(function() {

    if (typeof kode !== "undefined")
        cariObat(kode);
    
    if (isEmpty(pembelian))
        $('.date2').datetimepicker({
            format: "DD MMM YYYY HH:mm",
            showClear: false,
            showTodayButton: true,
            useCurrent: false,
            allowInputToggle: true,
            minDate: d
        });
    else {
        $('.date2').datetimepicker({
            format: "DD MMM YYYY HH:mm",
            showClear: false,
            showTodayButton: true,
            useCurrent: false,
            allowInputToggle: true,
        });
        
        jQuery.each(pembelian.transaksi_po, function (i, val) {
            generateTable(val.obat_po, val.obat_po.kode);
        });	
    }

    $('body').on("propertychange input", '.table-responsive input', function (e) {
        checkTotal();
        return false;  
    });  

    $('#kode-obat').keypress(function (e) {
        var key = e.which;
        if(key == 13)  // the enter key code
        {
            cariObat();
            return false;  
        }
    });  

    $("#cari-obat").click(function(){
        cariObat();
    });

    $("#simpan").click(function(){
        if(!$("#simpan").is(":disabled"))
        {
            callLoader();
            jumlah = 0;
            for (i = 1; i <= total_obat; i++) {
                jumlah += parseInt($("#jumlah-" + i).val());
            }
            supplier = $('#supplier').val();
            if ($('#total').length > 0)
                total = getNumber($('#total').val());
            else
                total = 0
            tgl_transaksi = $('#tgl_transaksi').val();
            jenis = jenis;
            no_transaksi = $('#nomor-transaksi').val();

            $.ajax({
                url : base_url + 'pembelian-reguler/remote',
                method : 'post',
                data: {
                    action:'simpan-pembelian',
                    supplier: supplier,
                    jumlah: jumlah,
                    total_harga: total,
                    tanggal:tgl_transaksi,
                    jenis:jenis,
                    no_transaksi: no_transaksi,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }).done(function(data){
                data = JSON.parse(data);
                if(data.status == 'sukses')
                {
                    id_pembelian = data.id;
                    if(id_pembelian !== 'undefined')
                    for(i=1;i<=total_obat;i++)
                    { 
                        if($("#jumlah-"+i).length > 0)
                        {
                            if ($('#harga-' + i).length > 0)
                                harga = getNumber($('#harga-' + i).val());
                            else
                                harga = 0;
                            if ($('#total-' + i).length > 0)
                                total = getNumber($('#total-' + i).html());
                            else
                                total = 0
                            $.ajax({
                                url : base_url + 'pembelian-reguler/remote',
                                method : 'post',
                                data: {
                                    action:'simpan-transaksi',
                                    id_pembelian:id_pembelian,
                                    kode_obat:$('#kode-'+i).val(),
                                    kategori_obat: $('#kategori-' + i).val(),
                                    nama_obat: $('#nama-' + i).val(),
                                    satuan_obat: $('#satuan-' + i).val(),
                                    harga: harga,
                                    total: total,
                                    jumlah_obat:$('#jumlah-'+i).val(),
                                    jenis : jenis
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                            }).done(function(data){
                                data = JSON.parse(data);
                                if(data.status != 'sukses')
                                    callNoty('error','Simpan error.');
                                else {
                                    console.log(i);
                                    console.log(total_obat);
                                    if (i == total_obat+1) {
                                        endLoader();
                                        callNoty('success', 'Berhasil Simpan pembelian.');
                                        swal({
                                            title: "Berhasil Simpan pembelian.",
                                            type: "success",
                                            showCancelButton: false,
                                            confirmButtonClass: "btn-success",
                                            confirmButtonText: "Oke",
                                            showLoaderOnConfirm: true,
                                            closeOnConfirm: false
                                        }, function (isConfirm) {
                                            if (isEmpty(pembelian) && jenis == 'po')
                                            {
                                                url = base_url + 'pembelian-reguler/print?transaksi=' + no_transaksi;
                                                window.open(
                                                    url,
                                                    '_blank'
                                                );
                                            }
                                            location.href = base_url + 'pembelian-reguler';
                                        });
                                    }
                                }
                            }).fail(function(jqXHR, textStatus, errorThrown){
                                if (jqXHR.status == 444)
                                    sessionExpireHandler();
                                else
                                    callNoty('warning');
                            });
                        }
                    }
                }
                else
                    callNoty('error','Simpan error.');
            }).fail(function(jqXHR, textStatus, errorThrown){
                if (jqXHR.status == 444)
                    sessionExpireHandler();
                else
                    callNoty('warning');
            });
        }
    });

})