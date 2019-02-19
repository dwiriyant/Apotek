var d = new Date();
d.setHours(0,0,0,0);

function callbackForm(){
    $('.counter').each(function(dom, index){
        var obj = $(this);
        $('#'+obj.attr('id')).simplyCountable({
            counter: '#'+obj.attr('id')+'-counter',
            countType: 'characters',
            maxCount: obj.attr('data-length'),
            strictMax: true,
            countDirection: 'down',
        });
    });

    // set icheck
    $('input[type="checkbox"], input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });

    // set form validation rules
    var edit = $('#form-id').val() ? true : false ;


    var rules = {
        name : 'required',
        start_date_tmp : 'required',
        end_date_tmp : 'required',
    };

    var rule = rules;
    $('#socmed-form').validate({
        rules : rule,
        submitHandler: function(form) {
                form.submit();
        }
    });

    $('.date').datetimepicker({
        format: "D MMM YYYY",
        showClear : true,
        showTodayButton : true,
        useCurrent : false,
        allowInputToggle : true,
    });

    $(".date").on("dp.change", function (e) {
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        var obj = $(this).parents('.form-group:eq(0)').find('input.dt-value:eq(0)');
        obj.val(value);
    });

    $('.date2').datetimepicker({
        format: "D MMM YYYY",
        showClear : true,
        showTodayButton : true,
        useCurrent : false,
        allowInputToggle : true,
    });

    $(".date2").on("dp.change", function (e) {
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        var obj = $(this).parents('.form-group:eq(0)').find('input.dt-value:eq(0)');
        obj.val(value);
    });

    Holder.run({
        images : '#form-photo'
    });
}

$(function() {
    Holder.run();
    callbackForm();

    $('.date').datetimepicker({
        format: "DD MMM YYYY",
        showClear : true,
        showTodayButton : true,
        useCurrent : false,
        allowInputToggle : true,
    });

    $('.date2').datetimepicker({
        format: "DD MMM YYYY",
        showClear : true,
        showTodayButton : true,
        useCurrent : false,
        allowInputToggle : true,
        minDate:d
    });

    $("#datetimepicker01").on("dp.change", function (e) {
        $('#datetimepicker02').data("DateTimePicker").minDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        var obj = $(this).prev().val(value);
    });

    $("#datetimepicker02").on("dp.change", function (e) {
        $('#datetimepicker01').data("DateTimePicker").maxDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        var obj = $(this).prev().val(value);
    });

    var startDate = $('#datetimepicker01').data("DateTimePicker").date();
    if (startDate)
        $('#datetimepicker02').data("DateTimePicker").minDate(startDate);
    var endDate = $('#datetimepicker02').data("DateTimePicker").date();
    if (endDate)
        $('#datetimepicker01').data("DateTimePicker").maxDate(endDate);


    $(document).on('click', '#upload-local', function(e){
        e.preventDefault();
        $('#form-file').click();
    });

    $(document).on('click', '.button-edit, .button-reset', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ajaxLoadForm(url, callbackForm);
    });

})

