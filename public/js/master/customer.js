var d = new Date();
d.setHours(0,0,0,0);

function callbackForm(){

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
        nama : 'required',
        kode : 'required',
        harga_satuan : 'required',
    };

    var rule = rules;
    $('#socmed-form').validate({
        rules : rule,
        submitHandler: function(form) {
                form.submit();
        }
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

    $('.date2').datetimepicker({
        format: "DD MMM YYYY",
        showClear : true,
        showTodayButton : true,
        useCurrent : false,
        allowInputToggle : true,
        minDate:d
    });

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

