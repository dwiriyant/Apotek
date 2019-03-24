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
