$(function () {
    $('input[type="checkbox"], input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
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
        var obj = $('input[name="'+$("#datetimepicker1").attr('rel')+'"]').val(value);
    });

    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        var obj = $('input[name="'+$("#datetimepicker2").attr('rel')+'"]').val(value);
    });

    $("#datetimepicker12").on("dp.change", function (e) {
        $('#datetimepicker22').data("DateTimePicker").minDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        var obj = $('input[name="'+$("#datetimepicker12").attr('rel')+'"]').val(value);
    });

    $("#datetimepicker22").on("dp.change", function (e) {
        $('#datetimepicker12').data("DateTimePicker").maxDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        var obj = $('input[name="'+$("#datetimepicker22").attr('rel')+'"]').val(value);
    });


    $('.ajax-checkbox').on('ifChanged', function (e) {
        var val = $(this).is(':checked') ? 1 : 0;
        $.ajax({
            url : base_url + route + '/remote',
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

    //delete community
    $(document).on('click', '.confirm_move', function(e){
        e.preventDefault();
        var id = $(this).attr('id');
        swal({
            title: "Are you sure want to move it?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            showLoaderOnConfirm: true,
            closeOnConfirm: false
        },function(isConfirm){
            if (isConfirm)
            {

              $.ajax({
	            url : base_url + route + '/remote',
	            method : 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
	            dataType: 'JSON',
	            data : {
	              action : 'move-news',
	              id : id,
	            },
	            beforeSend : function(){
	              callLoader();
	            }
	          }).always(function(){
	            endLoader();
	          }).done(function(data){
	            swal("Moved!",'Article has been moved', "success");
	            location.reload();
	            
	          }).fail(function(){
	            callNoty('warning');
	          });
            }
        });

    });

    
});