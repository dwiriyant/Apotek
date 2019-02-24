var d = new Date();
d.setHours(0,0,0,0);

function callbackForm(){

    // set form validation rules
    var edit = $('#form-id').val() ? true : false ;


    var rules = {
        nama : 'required',
    };

    var rule = rules;
    $('#supplier-form').validate({
        rules : rule,
        submitHandler: function(form) {
                form.submit();
        }
    });

    Holder.run({
        images : '#form-photo'
    });
}

$(function() {
    Holder.run();
    callbackForm();

    $(document).on('click', '.button-edit, .button-reset', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ajaxLoadForm(url, callbackForm);
    });

})

