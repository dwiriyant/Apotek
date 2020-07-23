$(document).scannerDetection({
    timeBeforeScanTest: 200, // wait for the next character for upto 200ms
    avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms

    endChar: [13],
    onComplete: function (barcode, qty) {
        $(document).focus();
        cariObat(barcode);
    },
    onError: function (string, qty) {
    }

});

$("#button-popup").click(function () {
    cariObatPopup();
    $("#popup-obat").modal('show');
});

$("#obat-search").click(function () {
    cariObatPopup();
});

$('#obat-keyword').keyup(function (e) {
    var key = e.which;
    if (key == 13)  // the enter key code
    {
        cariObatPopup();
        return false;
    }
});

function cariObatPopup()
{
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
$('#simpan-cetak').prop('disabled', true);
var d = new Date();
d.setHours(0, 0, 0, 0);
var total_obat = 0;
var total_harga = 0;
var diskon = 0;
var harga_diskon = 0;
var jasa_resep = 0;

function checkButton() {
    if ($('#data-obat').html() == '') {
        $('#simpan').prop('disabled', true);
        $('#simpan-cetak').prop('disabled', true);
    } else {
        $('#simpan').prop('disabled', false);
        $('#simpan-cetak').prop('disabled', false);
    }
}
function hapusObat(id) {
    swal({
        title: "hapus data?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        showLoaderOnConfirm: true,
        closeOnConfirm: true
    }, function (isConfirm) {
        $('#list-' + id).remove();
        reorderNomor();
        if ($('#data-obat').html() == '') {
            $("#data-kosong").show();
            total_obat = 0;
        }
        checkButton();
    });
}

function reorderNomor() {
    $(".nomor").html('');
    var table = document.getElementsByTagName('table')[0],
        rows = table.getElementsByTagName('tr'),
        text = 'textContent' in document ? 'textContent' : 'innerText';

    for (var i = 1, len = rows.length; i < len; i++) {
        rows[i].children[0][text] = i - 1 + '.' + rows[i].children[0][text];
    }

    $(".pack-obat").change(function () {
        ids = $(this).data('id');
        
        if (this.checked) {
            $(this).val('1');
            $('#text-'+ids).text('Ya');

            $('#harga-' + ids).text('Rp. '+formatMoney($('#harga-pack-'+ids).val()));
        } else{
            $(this).val('0');
            $('#text-' + ids).text('Tidak');
            $('#harga-' + ids).text('Rp. ' +formatMoney($('#harga-satuan-' + ids).val()));
        }
        checkTotal();
    });

}

function checkTotal() {
    
    total_harga = 0;
    for (i = 1; i <= total_obat; i++) {
        if ($("#total-" + i).length > 0)
            total_harga += parseInt($("#total-" + i).html());

        if ($("#jumlah-" + i).length > 0) {
            int_jumlah_obat = parseInt(getNumber($("#jumlah-" + i).val()));
            if (isNaN(int_jumlah_obat))
                return false;

            if (int_jumlah_obat <= 0) {
                int_jumlah_obat = 1;
                $("#jumlah-" + i).val('1');
            }
        }
        if ($("#harga-" + i).length > 0) {
            int_total_obat = parseInt(getNumber($("#harga-" + i).text()));
        }

        if (($("#harga-" + i).length > 0) && ($("#jumlah-" + i).length > 0)) {
            sub_total = int_jumlah_obat * int_total_obat;
            diskon_obat = parseInt(getNumber($("#diskon-" + i).val()));
            if (isNaN(diskon_obat))
                return false;

            $("#diskon-" + i).val(formatMoney(diskon_obat));
            sub_total = sub_total - diskon_obat;
            $("#total-" + i).html(sub_total);
        }
    }

    $('#total').val(total_harga);

    if (isNaN(diskon)) {
        diskon = 0;
    }

    if (diskon < 0 || diskon > 100) {
        $('#diskon').val('0');
        diskon = 0;
    }

    harga_diskon = total_harga - (total_harga * diskon / 100)

    jasa_resep = $('#jasa-resep').val().length > 0 ? $('#jasa-resep').val() : '0';
    jasa_resep = parseInt(getNumber(jasa_resep));
    if (isNaN(jasa_resep))
        jasa_resep = 0;
    if (jasa_resep < 0) {
        $('#jasa-resep').val('0');
        jasa_resep = 0;
    }

    harga_diskon = harga_diskon + jasa_resep;

    $('#total-harga').val(harga_diskon);
    $('#total-atas').html(harga_diskon.format());

    uang = $('#uang').val().length > 0 ? $('#uang').val() : '0';
    uang = parseInt(getNumber(uang));
    if (isNaN(uang))
        uang = 0;
    if (uang < 0) {
        $('#uang').val('0');
        uang = 0;
    }
    kembali = uang - harga_diskon;

    $('#uang-kembali').val(kembali.format());

    $('.currency2').each(function () {
        $(this).maskMoney('mask', $(this).val());
    })

    checkButton();
}

function cariObat(kode_obat = '') {
    if (kode_obat == '')
        id_obat = $('#kode-obat').val();
    else
        id_obat = kode_obat;

    if (id_obat == '')
        return false;

    $.ajax({
        url: base_url + 'penjualan-reguler/remote',
        method: 'post',
        data: { action: 'cari-obat', id: id_obat },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            callLoader();
        }
    }).always(function () {
        endLoader();
    }).done(function (data) {
        data = JSON.parse(data);
        if (data.data != null) {
            data = data.data;

            for (i = 1; i <= total_obat; i++) {
                if ($("#total-" + i).length > 0) {
                    if ($("#kode-" + i).text() == data.kode) {
                        $("#jumlah-" + i).val(parseInt($("#jumlah-" + i).val()) + 1);
                        checkTotal();
                        return false;
                    }
                }
            }
            
            total_obat++;
            result = '<tr id="list-' + total_obat + '"> ' +
                '<td class="nomor"></td>' +
                '<td id="kode-' + total_obat + '">' + (data.kode) + '</td>' +
                '<td>' + (data.nama) + '</td>' +
                '<td>' + (data.kategori.nama) + '</td>' +
                '<td>' + (data.satuan) + '</td>' +
                '<td>' + (data.type == 1 ? 'Sendiri' : 'Konsinyasi') + '</td>' +
                '<td id="harga-' + total_obat + '"> Rp. ' + formatMoney(jenis == 'resep' ? data.harga_jual_resep: data.harga_jual_satuan) + '</td>' +
                '<td><input style="max-width: 55px;" id="jumlah-' + total_obat + '" class="jumlah-obat" style="border: 0;" type="number" value="1"></td>' +
                '<td><input style="max-width: 75px;" id="diskon-' + total_obat + '" class="diskon-obat" style="border: 0;" type="number" value="0"></td>' +
                '<td id="total-' + total_obat + '"> Rp. ' + formatMoney(jenis == 'resep' ? data.harga_jual_resep : data.harga_jual_satuan) + '</td>';
            if(jenis == 'resep')
            {
                result +=
                    '<td onclick="hapusObat(' + total_obat + ')" style="cursor:pointer;" data-id="' + total_obat + '" class="hapus-data"><i style="color:red" class="fa fa-times"></i> </td>' +
                    '</tr>';
            } else
            {
                result +=
                    '<td><input  id="pack-' + total_obat + '" data-id="' + total_obat + '" class="pack-obat" type="checkbox" value="0"> <span id="text-' + total_obat + '">Tidak</span></td>' +
                    '<td onclick="hapusObat(' + total_obat + ')" style="cursor:pointer;" data-id="' + total_obat + '" class="hapus-data"><i style="color:red" class="fa fa-times"></i> </td>' +
                    '<td style="display: none;"><input style="display:none;" id="harga-satuan-' + total_obat + '" value="' + data.harga_jual_satuan + '" ></td>' +
                    '<td style="display: none;"><input style="display:none;" id="harga-pack-' + total_obat + '" value="' + data.harga_jual_pack + '" ></td>' +
                    '</tr>';
            }
            
            $("#data-kosong").hide();
            $("#data-obat").append(result);
            checkTotal();
            reorderNomor();
            $("#popup-obat").modal('hide');
            checkTotal();
        }
        else
            callNoty('error', 'Maaf data tidak ditemukan.');
    }).fail(function (jqXHR, textStatus, errorThrown) {
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
}

$(function () {

    $('.date2').datetimepicker({
        format: "DD MMM YYYY HH:mm",
        showClear: false,
        showTodayButton: true,
        useCurrent: false,
        allowInputToggle: true,
        minDate: d
    });

    $('body').on("keyup input", '.table-responsive input', function (e) {
        checkTotal();
        return false;
    });

    $('#kode-obat').keyup(function (e) {
        var key = e.which;
        if (key == 13)  // the enter key code
        {
            cariObat();
            return false;
        }
    });

    $('#diskon').on('keyup input', function (e) {

        diskon = parseInt($('#diskon').val());

        checkTotal();
    });

    $('#jasa-resep').on('keyup input', function (e) {

        checkTotal();
    });

    $('#uang').maskMoney({ prefix: '', thousands: '.', decimal: ',', precision: 0 }).on('keyup.maskMoney', function () {

        checkTotal();
    });

    $("#cari-obat").click(function () {
        cariObat();
    });

    $("#simpan").click(function () {
        if (!$("#simpan").is(":disabled")) {
            callLoader();

            jumlah = 0;
            for (i = 1; i <= total_obat; i++) {
                jumlah += parseInt($("#jumlah-" + i).val());
            }
            uang = getNumber($('#uang').val());
            jasa_resep = getNumber($('#jasa-resep').val());
            diskon = $('#diskon').val().length > 0 ? getNumber($('#diskon').val().toString()) : 0;
            total_harga = getNumber($('#total-harga').val());
            total = getNumber($('#total').val());
            tgl_transaksi = $('#tgl_transaksi').val();
            customer = $('#customer').val();
            dokter = $('#dokter').length > 0 ? $('#dokter').val() : '';
            jenis = jenis;
            no_transaksi = $('#nomor-transaksi').val();

            $.ajax({
                url: base_url + 'penjualan-reguler/remote',
                method: 'post',
                data: {
                    action: 'simpan-penjualan',
                    jumlah: jumlah,
                    uang: uang,
                    jasa_resep: jasa_resep,
                    total: total,
                    diskon: diskon,
                    total_harga: total_harga,
                    tanggal: tgl_transaksi,
                    jenis:jenis,
                    customer: customer,
                    dokter: dokter,
                    no_transaksi: no_transaksi,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }).done(function (data) {
                data = JSON.parse(data);
                if (data.status == 'sukses') {
                    id_penjualan = data.id;
                    if (id_penjualan.length !== 'undefined')
                        for (i = 1; i <= total_obat; i++) {
                            if ($("#total-" + i).length > 0) {
                                if($('#pack-' + i).length > 0)
                                    jual_pack = $('#pack-' + i).val();
                                else
                                    jual_pack = '0';
                                $.ajax({
                                    url: base_url + 'penjualan-reguler/remote',
                                    method: 'post',
                                    data: {
                                        action: 'simpan-transaksi',
                                        id_penjualan: id_penjualan,
                                        kode_obat: $('#kode-' + i).html(),
                                        harga: getNumber($('#harga-' + i).html()),
                                        diskon: getNumber($('#diskon-' + i).val()),
                                        total: getNumber($('#total-' + i).html()),
                                        jumlah_obat: $('#jumlah-' + i).val(),
                                        jual_pack: jual_pack,
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                }).done(function (data) {
                                    data = JSON.parse(data);
                                    if (data.status != 'sukses') {
                                        endLoader();
                                        callNoty('error', 'Simpan error.');
                                    } else {
                                        if(i==total_obat+1)
                                        {
                                            endLoader();
                                            callNoty('success', 'Berhasil Simpan Penjualan.');

                                            swal({
                                                title: "Berhasil Simpan Penjualan.",
                                                type: "success",
                                                showCancelButton: false,
                                                confirmButtonClass: "btn-success",
                                                confirmButtonText: "Oke",
                                                showLoaderOnConfirm: true,
                                                closeOnConfirm: false
                                            }, function (isConfirm) {
                                                    
                                                location.reload();
                                            });                                            
                                        }
                                    }
                                }).fail(function (jqXHR, textStatus, errorThrown) {
                                    if (jqXHR.status == 444)
                                        sessionExpireHandler();
                                    else
                                        callNoty('warning');
                                });
                            }
                        }
                }
                else
                    callNoty('error', 'Simpan error.');
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 444)
                    sessionExpireHandler();
                else
                    callNoty('warning');
            });
        }
    });

    $("#simpan-cetak").click(function () {
        if (!$("#simpan-cetak").is(":disabled")) {
            callLoader();

            jumlah = 0;
            for (i = 1; i <= total_obat; i++) {
                jumlah += parseInt($("#jumlah-" + i).val());
            }
            uang = getNumber($('#uang').val());
            jasa_resep = getNumber($('#jasa-resep').val());
            diskon = $('#diskon').val().length > 0 ? getNumber($('#diskon').val().toString()) : 0;
            total_harga = getNumber($('#total-harga').val());
            total = getNumber($('#total').val());
            tgl_transaksi = $('#tgl_transaksi').val();
            customer = $('#customer').val();
            dokter = $('#dokter').length > 0 ? $('#dokter').val() : '';
            jenis = jenis;
            no_transaksi = $('#nomor-transaksi').val();

            $.ajax({
                url: base_url + 'penjualan-reguler/remote',
                method: 'post',
                data: {
                    action: 'simpan-penjualan',
                    jumlah: jumlah,
                    uang: uang,
                    jasa_resep: jasa_resep,
                    total: total,
                    diskon: diskon,
                    total_harga: total_harga,
                    tanggal: tgl_transaksi,
                    jenis: jenis,
                    customer: customer,
                    dokter: dokter,
                    no_transaksi: no_transaksi,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }).done(function (data) {
                data = JSON.parse(data);
                if (data.status == 'sukses') {
                    id_penjualan = data.id;
                    if (id_penjualan.length !== 'undefined')
                        for (i = 1; i <= total_obat; i++) {
                            if ($("#total-" + i).length > 0) {
                                if ($('#pack-' + i).length > 0)
                                    jual_pack = $('#pack-' + i).val();
                                else
                                    jual_pack = '0';
                                $.ajax({
                                    url: base_url + 'penjualan-reguler/remote',
                                    method: 'post',
                                    data: {
                                        action: 'simpan-transaksi',
                                        id_penjualan: id_penjualan,
                                        kode_obat: $('#kode-' + i).html(),
                                        harga: getNumber($('#harga-' + i).html()),
                                        total: getNumber($('#total-' + i).html()),
                                        diskon: getNumber($('#diskon-' + i).val()),
                                        jumlah_obat: $('#jumlah-' + i).val(),
                                        jual_pack: jual_pack,
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                }).done(function (data) {
                                    data = JSON.parse(data);
                                    if (data.status != 'sukses') {
                                        endLoader();
                                        callNoty('error', 'Simpan error.');
                                    } else {
                                        if (i == total_obat+1) {
                                            endLoader();
                                            callNoty('success', 'Berhasil Simpan Penjualan.');

                                            swal({
                                                title: "Berhasil Simpan Penjualan.",
                                                type: "success",
                                                showCancelButton: false,
                                                confirmButtonClass: "btn-success",
                                                confirmButtonText: "Oke",
                                                showLoaderOnConfirm: true,
                                                closeOnConfirm: false
                                            }, function (isConfirm) {
                                                url = base_url + 'penjualan-reguler/print?transaksi=' + no_transaksi;
                                                window.open(
                                                    url,
                                                    '_blank'
                                                );
                                                location.reload();
                                            });
                                        }
                                    }
                                }).fail(function (jqXHR, textStatus, errorThrown) {
                                    if (jqXHR.status == 444)
                                        sessionExpireHandler();
                                    else
                                        callNoty('warning');
                                });
                            }
                        }
                }
                else
                    callNoty('error', 'Simpan error.');
            }).fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 444)
                    sessionExpireHandler();
                else
                    callNoty('warning');
            });
        }
    });

})

