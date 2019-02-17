$(function(){
    $('input[type="checkbox"], input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });

    $('.popovers').popover();

    $(".colorbox-group").colorbox({rel:'colorbox-group', maxWidth : "90%"});

    //copy link
    new Clipboard('button.btn-copy');
    $("button.btn-copy").click(function(){
        callNoty('success', 'Success copying url.');
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

    var startDate = $('#datetimepicker1').data("DateTimePicker").date();
    if (startDate)
        $('#datetimepicker2').data("DateTimePicker").minDate(startDate);
    var endDate = $('#datetimepicker2').data("DateTimePicker").date();
    if (endDate)
        $('#datetimepicker1').data("DateTimePicker").maxDate(endDate);

    $('.btn-meta').click(function(){
        $.ajax({
            url : base_url + 'images-bank/meta?&id='+$(this).parents('div.box:eq(0)').attr('data-id'),
            type : 'get',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend : function(){
                callLoader();
            }
        }).always(function(){
            endLoader();
        }).done(function(data){
            $('#modal-ajax-title').html('Edit Image Metadata');
            $('#modal-ajax-body').html(data);
            $('#modal-ajax').modal();

            $('.datetime-modal').datetimepicker({
                format: "ddd, D MMM YYYY HH:mm",
                showTodayButton : true,
                allowInputToggle : true,
            });

            $("#datetimepicker-modal").on("dp.change", function (e) {
                var value = '';
                if (e.date)
                    value = e.date.format('YYYY-MM-DD HH:mm:ss');
                var obj = $(this).parents('.form-group:eq(0)').find('input.dt-value:eq(0)');
                obj.val(value);
            });

            var rule = {
                photo_title : 'required',
                photo_copyright : 'required',
                photo_date_input : 'required',
                photo_photographer : 'required',
                photo_caption : 'required',
            };

            $('#form-meta').validate({
                rules : rule,
                submitHandler: function(form) {
                    var photo_data = {
                        id : $('#modal-photo-id').val(),
                        photo_title : $('#modal-photo-title').val(),
                        photo_copyright : $('#modal-photo-copyright').val(),
                        photo_date : $('#modal-photo-date-value').val(),
                        photo_photographer : $('#modal-photo-photographer').val(),
                        photo_location : $('#modal-photo-location').val(),
                        photo_caption : $('#modal-photo-caption').val(),
                        photo_keywords : $('#modal-photo-keywords').val(),
                    };
                    $.ajax({
                        type: 'POST',
                        url: base_url + 'images-bank/meta',
                        data: photo_data,
                        dataType : 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend : function(){
                            callLoader();
                        }
                    }).always(function () {
                        endLoader();
                    }).done(function (data) {
                        $('div.photo-box[data-id="'+data.photo_id+'"] div.photo-container img').popover({
                            trigger : 'hover',
                            title : '<strong>By</strong> : '+data.photo_photographer+' <br /><small style=\'font-size:11px;\'>'+data.photo_copyright+'</small>',
                            content: data.photo_event,
                            html : true,
                        });
                        $('div.photo-box[data-id="'+data.photo_id+'"] div.photo-container img').popover();
                        $('div.photo-box[data-id="'+data.photo_id+'"] span.title-cont').html(data.photo_title);
                        $('div.photo-box[data-id="'+data.photo_id+'"] span.date-cont').html(data.photo_date);
                        callNoty('success', 'Metadata has been saved successfully.')
                    }).fail(function(jqXHR, textStatus, errorThrown){
                        if (jqXHR.status == 444)
                            sessionExpireHandler();
                        else
                            callNoty('warning');
                    });
                    $('#modal-ajax').modal('hide');
                    return false;
                }
            });

        }).fail(function(jqXHR, textStatus, errorThrown){
            if (jqXHR.status == 444)
                sessionExpireHandler();
            else
                callNoty('warning');
        });
    });

    $('.btn-crop').click(function(){
        crop_src = $(this).attr('data-src');
        $('#cropper > img').attr('src', crop_src);
        $('#cropper-modal').modal();
        $('#apply-crop').attr('data-id', $(this).parents('div.box:eq(0)').attr('data-id'));
    });

    $('#apply-crop').click(function(){
        var dataCrop = $('#cropper > img').cropper('getData');
        var data_post = {
            id : $(this).attr('data-id'),
            x : dataCrop.x,
            y : dataCrop.y,
            w : dataCrop.width,
            h : dataCrop.height
        };
        $.ajax({
            url : base_url + 'images-bank/crop',
            type : 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data : data_post,
            dataType : 'json',
            beforeSend : function(){
                callLoader();
            }
        }).always(function(){
            endLoader();
            $('#cropper-modal').modal('hide');
        }).done(function(data){
            $('div.photo-box[data-id="'+data.photo_id+'"] div.photo-container img').attr('src', data.photo_thumb);
            $('div.photo-box[data-id="'+data.photo_id+'"] button.btn-crop').attr('data-src', data.photo_real);
        }).fail(function(jqXHR, textStatus, errorThrown){
            if (jqXHR.status == 444)
                sessionExpireHandler();
            else
                callNoty('warning');
        });
    });

    $('#crop-ratio').change(function(){
        var ratio = $(this).val();
        if (ratio == 'free')
            $('#cropper > img').cropper("setAspectRatio", NaN);
        else{
            ratio = ratio.split(':');
            $('#cropper > img').cropper("setAspectRatio",ratio[0] / ratio[1]);
        }
    });

    $('.btn-rotate').click(function(){
        var data_post = {
            id : $(this).parents('div.box:eq(0)').attr('data-id'),
            deg : $(this).attr('data-deg'),
        };
        $.ajax({
            url : base_url + 'images-bank/rotate',
            type : 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data : data_post,
            dataType : 'json',
            beforeSend : function(){
                callLoader();
            }
        }).always(function(){
            endLoader();
        }).done(function(data){
            console.log(data);
            $('div.photo-box[data-id="'+data.photo_id+'"] div.photo-container img').attr('src', data.photo_thumb);
            $('div.photo-box[data-id="'+data.photo_id+'"] button.btn-crop').attr('data-src', data.photo_real);
            $('div.photo-box[data-id="'+data.photo_id+'"] div.size-cont').html(data.size);
        }).fail(function(jqXHR, textStatus, errorThrown){
            if (jqXHR.status == 444)
                sessionExpireHandler();
            else
                callNoty('warning');
        });
    });

    $('#cropper-modal').on('shown.bs.modal', function () {
          $('#cropper-loader').show();
          $('#cropper > img').cropper({
            dragCrop: false,
            checkCrossOrigin : false,
            checkOrientation : false,
            autoCropArea: 0.5,
            guides:false
          });
          $('#cropper > img').on('built.cropper', function (e) {
              $('#cropper-loader').hide();
          });
    }).on('hidden.bs.modal', function () {
        $('#cropper > img').cropper('destroy');
        $('#crop-ratio option:eq(0)').prop('selected', true);
    });
})