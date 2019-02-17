function addRules(rulesObj){
    for (var item in rulesObj){
        if (item !== 'input_schedule_start' && item !== 'input_schedule_end' && item !== 'order')
            $('input[name="'+item+'"]').rules('add',rulesObj[item]);
    }
}

function removeRules(rulesObj){
    for (var item in rulesObj){
        if (item !== 'input_schedule_start' && item !== 'input_schedule_end' && item !== 'order')
            $('input[name="'+item+'"]').rules('remove');
    }
}


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
        news_date_publish : 'required',
        author : 'required',
        author_url : 'required',
        link:{
            required: true,
            url: true
        },
        title: {
            required: true
        }
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
        format: "D MMM YYYY HH:mm",
        showClear : true,
        showTodayButton : true,
        useCurrent : false,
        allowInputToggle : true,
    });

    $(".date2").on("dp.change", function (e) {
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD HH:mm');
        var obj = $(this).parents('.form-group:eq(0)').find('input.dt-value:eq(0)');
        obj.val(value);
    });

    Holder.run({
        images : '#form-photo'
    });

    $('#submission').on('change', function(event){
      if($('#submission').val()=='campaign') {
        $("#campaign").show();
      }  
      else {
        $("#campaign").hide();
      }
    });

    if($('#submission').val()=='campaign') {
        $("#campaign").show();
    }  
    else {
        $("#campaign").hide();
    }  
}

$(function() {
    Holder.run();
    callbackForm();

    $('.date').datetimepicker({
        format: "DD MMM YYYY",
        showClear : true,
        showTodayButton : true,
        useCurrent : false,
    });

    $('.date2').datetimepicker({
        format: "DD MMM YYYY HH:mm",
        showClear : true,
        showTodayButton : true,
        useCurrent : false,
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


    $("#datetimepicker012").on("dp.change", function (e) {
        $('#datetimepicker022').data("DateTimePicker").minDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        var obj = $(this).prev().val(value);
    });

    $("#datetimepicker022").on("dp.change", function (e) {
        $('#datetimepicker012').data("DateTimePicker").maxDate(e.date);
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD');
        var obj = $(this).prev().val(value);
    });

    var startDate = $('#datetimepicker012').data("DateTimePicker").date();
    if (startDate)
        $('#datetimepicker022').data("DateTimePicker").minDate(startDate);
    var endDate = $('#datetimepicker022').data("DateTimePicker").date();
    if (endDate)
        $('#datetimepicker012').data("DateTimePicker").maxDate(endDate);


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

$('#crawl').click(function(){
    $('#modal-crawl').modal('show'); 
});

$('#modal-crawl').on('hidden.bs.modal', function () {
    location.reload();
})

$('#get-crawl').click(function(){
    $.ajax({
        url : base_url+"social-media/remote",
        data : {
            action : 'get-crawl',
            keyword : $('#keyword-crawl').val(),
            source : $('#source-crawl').val(),
            order : $('#order-crawl').val()
        },
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        type : 'post',
        dataType : 'json',
        beforeSend : function(){
            callLoader('Please wait while retrieving data.')
        }
    }).done(function(data){
        if(data.success == true)
            $('#result-crawl').html(data.list);
        else
            $('#result-crawl').html("Oppss.. Something wrong or data not found, please re-check your keyword");
    }).always(function(){
        endLoader();
    }).fail(function (jqXHR) {
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
    
});

function saveYoutube(atr) {
    $.ajax({
        url : base_url+"social-media/remote",
        data : {
            action : 'save-youtube',
            videoId : atr.getAttribute('data-id')
        },
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        type : 'post',
        dataType : 'json',
        beforeSend : function(){
            callLoader('Please wait while retrieving data.')
        }
    }).done(function(data){
        if(data.success == true)
        {
            swal('Done','Success Save Data.','success');
            atr.setAttribute('disabled','disabled');
        }
        else
            swal('FAILED','Failed while Saving Data.','error');

    }).always(function(){
        endLoader();
    }).fail(function (jqXHR) {
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
    
};

function saveInstagram(atr) {
    $.ajax({
        url : base_url+"social-media/remote",
        data : {
            action : 'save-instagram',
            videoId : atr.getAttribute('data-id')
        },
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        type : 'post',
        dataType : 'json',
        beforeSend : function(){
            callLoader('Please wait while retrieving data.')
        }
    }).done(function(data){
        if(data.success == true)
        {
            swal('Done','Success Save Data.','success');
            atr.setAttribute('disabled','disabled');
        }
        else
            swal('FAILED','Failed while Saving Data.','error');

    }).always(function(){
        endLoader();
    }).fail(function (jqXHR) {
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
    
};

$('#source-crawl').change(function() {
    if($('#source-crawl').val() == 'youtube'){
        $('#order-crawl').show();
        $('#order-label').show();
    }
    else{
        $('#order-label').hide();
        $('#order-crawl').hide();
    }
});

$('.ajax-editor_pick').on('ifChanged', function (e) {
    var val = $(this).is(':checked') ? 1 : 0;
    $.ajax({
        url : base_url+"social-media/remote",
        data : {id : $(this).attr('data-id'), value : val, action : 'editor_pick'},
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        type : 'post',
        dataType : 'json'
    }).fail(function () {
        callNoty('warning');
    });
})

$('.ajax-headline').on('ifChanged', function (e) {
    var val = $(this).is(':checked') ? 1 : 0;
    $.ajax({
        url : base_url+"social-media/remote",
        data : {id : $(this).attr('data-id'), value : val, action : 'headline'},
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        type : 'post',
        dataType : 'json'
    }).fail(function () {
        callNoty('warning');
    });
})

$('#section').click(function(){
    $.ajax({
        url : base_url+"social-media/remote",
        data : {
            action : 'get-section'
        },
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        type : 'post',
        dataType : 'json',
        beforeSend : function(){
            callLoader('Please wait while retrieving data.')
        }
    }).done(function(data){
        if(data.success == true)
        {
            if (data.data !== null)
            {
                if( data.data.latest_news == 1)
                    $('#latest-news').iCheck('check');
                if( data.data.top_article == 1)
                    $('#top-article').iCheck('check');
                if( data.data.top_videos == 1)
                    $('#top-videos').iCheck('check');
                if( data.data.top_images == 1)
                    $('#top-images').iCheck('check');
            }

            $('#modal-section').modal('show'); 
        } else
            callNoty('warning');
    }).always(function(){
        endLoader();
    }).fail(function (jqXHR) {
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
    
});

$('#save-section').click(function(){
    var news = $('#latest-news').is(':checked') ? 1 : 0;
    var article = $('#top-article').is(':checked') ? 1 : 0;
    var videos = $('#top-videos').is(':checked') ? 1 : 0;
    var images = $('#top-images').is(':checked') ? 1 : 0;
    $.ajax({
        url : base_url+"social-media/remote",
        data : {
            action : 'save-section',
            latest_news : news,
            top_article : article,
            top_videos : videos,
            top_images : images
        },
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        type : 'post',
        dataType : 'json',
        beforeSend : function(){
            callLoader('Please wait while retrieving data.')
        }
    }).done(function(data){
        if(data.success == true)
        {
            $('#modal-section').modal('hide'); 
            swal('Done','Success Save Data.','success');
        } else
            callNoty('warning');
    }).always(function(){
        endLoader();
    }).fail(function (jqXHR) {
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
    
});


$(document).bind('keydown', 'Return', function(e){
        key  = e.keyCode;
        if(key == 13){  
            if(($("#modal-crawl").data('bs.modal') || {}).isShown)
                $('#get-crawl').click();
            return false;
        }  
});  

$('#submission').on('change', function(event){
  if($('#submission').val()=='campaign') {
    $("#campaign").show();
  }  
  else {
    $("#campaign").hide();
  }
});

if($('#submission').val()=='campaign') {
    $("#campaign").show();
}  
else {
    $("#campaign").hide();
}  