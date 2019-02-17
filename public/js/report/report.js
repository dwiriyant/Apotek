Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };

$(function () {


    
    $('input[type="checkbox"], input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });

     $('.dates').datetimepicker({
        format: "ddd, D MMM YYYY HH:mm",
        showClear : true,
        showTodayButton : true,
        useCurrent : false,
        allowInputToggle : true,
    });

    $(".dates").on("dp.change", function (e) {
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD HH:mm:ss');
        var obj = $(this).parents('.form-group:eq(0)').find('input.dt-value:eq(0)');
        obj.val(value);
    });

    $('.date').datetimepicker({
        format: "DD MMM YYYY",
        showClear : true,
        showTodayButton : true,
        useCurrent : false,
    });

    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        $('input[name="'+$("#datetimepicker1").attr('rel')+'"]').val(value);
    });

    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        $('input[name="'+$("#datetimepicker2").attr('rel')+'"]').val(value);
    });

    $("#datetimepicker3").on("dp.change", function (e) {
        $('#datetimepicker4').data("DateTimePicker").minDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        $('input[name="'+$("#datetimepicker3").attr('rel')+'"]').val(value);
    });

    $("#datetimepicker4").on("dp.change", function (e) {
        $('#datetimepicker3').data("DateTimePicker").maxDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        $('input[name="'+$("#datetimepicker4").attr('rel')+'"]').val(value);
    });

    $("#datetimepicker5").on("dp.change", function (e) {
        $('#datetimepicker6').data("DateTimePicker").minDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        $('input[name="'+$("#datetimepicker5").attr('rel')+'"]').val(value);
    });

    $("#datetimepicker6").on("dp.change", function (e) {
        $('#datetimepicker5').data("DateTimePicker").maxDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        $('input[name="'+$("#datetimepicker6").attr('rel')+'"]').val(value);
    });

    // var startDate = $('#datetimepicker1').data("DateTimePicker").date();
    // if (startDate)
    //     $('#datetimepicker2').data("DateTimePicker").minDate(startDate);
    
    // var endDate = $('#datetimepicker2').data("DateTimePicker").date();
    // if (endDate)
    //     $('#datetimepicker1').data("DateTimePicker").maxDate(endDate);

    $('.ajax-checkbox').on('ifChanged', function (e) {
        var val = $(this).is(':checked') ? 1 : 0;
        $.ajax({
            url :  route + '/remote',
            data : {id : $(this).attr('data-id'), column : $(this).attr('data-column'), value : val, action : 'ajax-checkbox'},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type : 'post',
            dataType : 'json'
        }).fail(function () {
            callNoty('warning');
        });
    })


    // $("#lvl8").click(function () {
    //     console.log('zzz');
    //     $("#reject").attr("hidden","false");
    // });

    // $('#lvl1').iCheck('toggle', function(){
    //   console.log('Well done, Sir');
    // });

    $('.icheckbox_minimal-blue').removeClass("disabled");

    
});