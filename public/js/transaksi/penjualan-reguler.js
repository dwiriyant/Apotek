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
    int_jumlah_obat = parseInt(getNumber($("#jumlah-"+total_obat).val()));
    int_total_obat = parseInt(getNumber($("#harga-"+total_obat).text()));

    if(isNaN(int_jumlah_obat))
        return false;
    
    if(int_jumlah_obat <= 0)
    {
        int_jumlah_obat = 1;
        $("#jumlah-"+total_obat).val('1');
    }
    
    $("#total-"+total_obat).html(int_jumlah_obat * int_total_obat);
    total_harga = 0;
    for(i=1;i<=total_obat;i++)
    {
        if($("#total-"+i).length > 0)
            total_harga += parseInt($("#total-"+i).html());
    }

    $('#total').val(total_harga);

    if(isNaN(diskon))
    {
        diskon = 0;
    }

    if(diskon<0 || diskon > 100)
    {
        $('#diskon').val('0');
        diskon = 0;
    }

    harga_diskon = total_harga - (total_harga * diskon / 100)
    $('#total-harga').val(harga_diskon);
    $('#total-atas').html(harga_diskon.format());

    uang = $('#uang').val().length > 0 ? $('#uang').val() : '0';
    uang = parseInt(getNumber(uang));
    if(isNaN(uang))
        uang = 0;
    if(uang<0)
    {
        $('#uang').val('0');
        uang = 0;
    }
    kembali = uang - harga_diskon;
    
    $('#uang-kembali').val(kembali.format());

    $('.currency2').each(function(){ 
        $(this).maskMoney('mask', $(this).val());
    })

    checkButton();
}

function cariObat() {
    id_obat = $('#kode-obat').val();

    $.ajax({
        url : base_url + 'penjualan-reguler/remote',
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
        if(data.data != null)
        {
            data = data.data;

            for(i=1;i<=total_obat;i++)
            {
                if($("#total-"+i).length > 0)
                {
                    if($("#kode-"+i).text() == data.kode)
                    {
                        $("#jumlah-"+i).val(parseInt($("#jumlah-"+i).val())+1);
                        checkTotal();
                        return false;
                    }
                }
            }
            
            total_obat++;
            result = '<tr id="list-'+ total_obat+'"> ' +
                '<td class="nomor"></td>' +
                '<td id="kode-'+ total_obat+'">' + (data.kode) +'</td>' +
                '<td>' + (data.nama) +'</td>' +
                '<td>' + (data.kategori.nama) +'</td>' +
                '<td>' + (data.satuan) +'</td>' +
                '<td id="harga-'+ total_obat+'"> Rp. ' + formatMoney(data.harga_jual_satuan) +'</td>' +
                '<td><input style="max-width: 55px;" id="jumlah-'+ total_obat+'" class="jumlah-obat" style="border: 0;" type="number" value="1"></td>' +
                '<td id="total-'+ total_obat+'"> Rp. ' + formatMoney(data.harga_jual_satuan) +'</td>' +
                '<td onclick="hapusObat('+total_obat+')" style="cursor:pointer;" data-id="'+ total_obat+'" class="hapus-data"><i style="color:red" class="fa fa-times"></i> </td>' +
            '</tr>';
            $("#data-kosong").hide();
            $("#data-obat").append(result);
            checkTotal();
            reorderNomor();
        }
        else
            callNoty('error','Maaf data tidak ditemukan.');
    }).fail(function(jqXHR, textStatus, errorThrown){
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
}

$(function() {
    $('.date2').datetimepicker({
        format: "DD MMM YYYY HH:mm",
        showClear : false,
        showTodayButton : true,
        useCurrent : false,
        allowInputToggle : true,
        minDate:d
    });

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

    $('#diskon').on('propertychange input', function (e) {
        
        diskon = parseInt($('#diskon').val());
        
        checkTotal();
    });  

    $('#uang').maskMoney({prefix: '', thousands: '.', decimal: ',',precision: 0}).on('keyup.maskMoney', function () {
        
        checkTotal();
    });  

    $("#cari-obat").click(function(){
        cariObat();
    });

    $("#simpan").click(function(){
        if(!$("#simpan").is(":disabled"))
        {
            callLoader();

            jumlah = getNumber($('#total-harga').val());
            uang = getNumber($('#uang').val());
            diskon = $('#diskon').val().length > 0 ? getNumber($('#diskon').val().toString()) : 0;
            total = getNumber($('#total').val());
            total_harga = getNumber($('#total-harga').val());
            tgl_transaksi = $('#tgl_transaksi').val();

            $.ajax({
                url : base_url + 'penjualan-reguler/remote',
                method : 'post',
                data: {
                    action:'simpan-penjualan',
                    jumlah:jumlah,
                    uang:uang,
                    diskon:diskon,
                    total:total,
                    total_harga:total_harga,
                    tanggal:tgl_transaksi,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }).done(function(data){
                data = JSON.parse(data);
                if(data.status == 'sukses')
                {
                    id_penjualan = data.id;
                    if(id_penjualan.length !== 'undefined')
                    for(i=1;i<=total_obat;i++)
                    {
                        if($("#total-"+i).length > 0)
                        {
                            $.ajax({
                                url : base_url + 'penjualan-reguler/remote',
                                method : 'post',
                                data: {
                                    action:'simpan-transaksi',
                                    id_penjualan:id_penjualan,
                                    kode_obat:$('#kode-'+i).html(),
                                    harga: getNumber($('#harga-'+i).html()),
                                    total: getNumber($('#total-'+i).html()),
                                    jumlah_obat:$('#jumlah-'+i).val(),
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                            }).done(function(data){
                                data = JSON.parse(data);
                                if(data.status != 'sukses')
                                    callNoty('error','Simpan error.');
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

            endLoader();
            callNoty('success','Berhasil Simpan Penjualan.');
        }
    });

})

