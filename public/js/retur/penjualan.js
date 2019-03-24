$('.date').datetimepicker({
    format: "DD MMM YYYY",
    showClear: true,
    showTodayButton: true,
    useCurrent: false,
});

$("#datetimepicker1").on("dp.change", function (e) {
    $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
    var value = '';
    if (e.date)
        value = e.date.format('YYYY-MM-DD');
    var obj = $('input[name="' + $("#datetimepicker1").attr('rel') + '"]').val(value);
});

$("#datetimepicker2").on("dp.change", function (e) {
    $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
    var value = '';
    if (e.date)
        value = e.date.format('YYYY-MM-DD');
    var obj = $('input[name="' + $("#datetimepicker2").attr('rel') + '"]').val(value);
});

$(".get-detail").click(function () {
    id = $(this).data('id');
    getDetail(id);
    $("#popup-detail").modal('show');

});

function getDetail(id) {
    
    $.ajax({
        url: base_url + 'retur-penjualan/remote',
        method: 'post',
        data: { action: 'get-transaction', id: id },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            callLoader();
        }
    }).always(function () {
        endLoader();
    }).done(function (html) {
        $('#table-detail').html(html);

        $('.keterangan').on('keypress', function (e) {
            var id = $(this).data('id');
            var kode = $(this).data('kode');
            var keterangan = $('#keterangan-' + id).val();
            var retur = $('#retur-' + id).val();
            $("#keterangan-note-" + id).show();
            if (e.which == 13 && retur != '') {
                updateRetur(id,kode, retur, keterangan);
            }
        });

    }).fail(function (jqXHR, textStatus, errorThrown) {
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
}

function updateRetur(id,kode, retur, keterangan) {
    $.ajax({
        url: base_url + 'retur-penjualan/remote',
        method: 'post',
        data: {
            action: 'retur',
            id: id,
            kode: kode,
            retur: retur,
            keterangan: keterangan,
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            callLoader();
        }
    }).always(function () {
        endLoader();
    }).done(function (data) {
        $("#keterangan-note-" + id).hide();
        callNoty('success','Berhasil update data retur');
    }).fail(function (jqXHR, textStatus, errorThrown) {
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
}