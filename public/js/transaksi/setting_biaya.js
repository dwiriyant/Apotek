var d = new Date();
d.setHours(0,0,0,0);

function callbackForm(){

    // set form validation rules
    var edit = $('#form-id').val() ? true : false ;


    var rules = {
        nama : 'required',
    };

    var rule = rules;
    $('#setting-biaya-form').validate({
        rules : rule,
        submitHandler: function(form) {
                form.submit();
        }
    });

    $('.currency').maskMoney({prefix: 'Rp. ', 
                            thousands: '.', 
                            decimal: ',',
                            precision: 0
                          });
    $(".currency").keyup(function() {
      var clone = $(this).val();
      var cloned = clone.replace(/[A-Za-z$. ,-]/g, "")
      $('#'+$(this).data('currency')).val(cloned);
    });
    
    $('.currency').each(function(){ // function to apply mask on load!
        $(this).maskMoney('mask', $(this).val());
    })

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

    $('.date2').datetimepicker({
        format: "DD MMM YYYY",
        showClear : true,
        showTodayButton : true,
        useCurrent : false,
        allowInputToggle : true,
        minDate:d
    });

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

    $(document).on('click', '.button-edit, .button-reset', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ajaxLoadForm(url, callbackForm);
    });

})

