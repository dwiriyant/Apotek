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
        url: base_url + 'report-penjualan/remote',
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
    }).fail(function (jqXHR, textStatus, errorThrown) {
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
}