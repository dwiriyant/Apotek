function readURLToImg(input, image_id, photo_id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(theFile) {
            var image = new Image();
            image.src = theFile.target.result;

            image.onload = function() {
                // access image size here
                checkImageSize(this.width, this.height, image_id);
                $(image_id).attr('src', this.src).show();
                var next_elemet = $(image_id).next();
                if (next_elemet.is('div.img-polaroid'))
                    next_elemet.hide();
                $(image_id).parents('div.image-container:eq(0)').find('input[name$="crop_data"]:eq(0)').val('');
                if (image_id.indexOf('headline') > -1){
                    headlineChanged();
                }
                if (photo_id !== undefined || photo_id !== '')
                    $(photo_id).val('');
            };
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function ajaxLoadForm(url, callback){
    $.ajax({
        url : url,
        method : 'get',
        beforeSend : function(){
            callLoader();
        }
    }).always(function(){
        endLoader();
    }).done(function(html){
        $('#form-container').html(html);
        $("#form-container input:text, #formId textarea").first().focus();
        callback();
    }).fail(function(jqXHR, textStatus, errorThrown){
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
}

function callLoader(text) {
    if (text == '' || text == undefined)
        text = "Please wait while data is being processed";
    var type = 'information';
    var noty_loader = noty({
        text         : '<div class="activity-item"> <i class="fa fa-refresh fa-spin"></i> <div class="activity" style="font-size: 14px;font-weight: bold;"> '+text+' </div> </div>',
        type         : type,
        layout       : 'topRight',
        dismissQueue : true,
        killer       : true,
        maxVisible   : 1,
    });
}

function endLoader(){
    $.noty.closeAll()
}

function callNoty(type, text) {
    var notification_html = [];
    if ((text == undefined || text == '') && type == 'warning')
        text = "Sorry, system has failed to process your request. Please try again or contact your administrator.";
    notification_html['warning'] = '<div class="activity-item"> <i class="fa fa-tasks text-warning"></i> <div class="activity"> '+text+' </div> </div>',
    notification_html['error'] = '<div class="activity-item"> <i class="fa fa-close text-success"></i> <div class="activity"> '+text+' </div> </div>',
    notification_html['information'] = '<div class="activity-item"> <i class="fa fa-comment text-danger"></i> <div class="activity"> '+text+' </div> </div>',
    notification_html['success'] = '<div class="activity-item"> <i class="fa fa-check text-success"></i> <div class="activity"> '+text+' </div> </div>';
    var n = noty({
        text: notification_html[type],
        type: type,
        layout: 'topRight',
        dismissQueue : true,
        killer       : true,
        maxVisible   : 1,
    });
}

$('a.confirm').click(function(e){
    e.preventDefault();
    var url = $(this).attr('href');
    swal({
        title: "Are you sure?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        showLoaderOnConfirm: true,
        closeOnConfirm: false
    },function(isConfirm){
        if (isConfirm)
            window.location = url;
    });
});

$(document).on('click', '.image-bank-upload-btn', function(e){
    e.preventDefault();
    var title = $($(this).attr('data-title')).val();
    if (title === undefined || title === ''){
        callNoty('error', $(this).attr('data-message'));
        $($(this).attr('data-title')).focus();
    }else{
        var dimension = $(this).attr('data-dimension');
        if (dimension){
            $('#modal-upload-img').attr('dimension-data', dimension).attr('src', 'holder.js/'+dimension+'/?auto=yes');
            Holder.run({
                images : '#modal-upload-img'
            });
        }
        $('#modal-upload-input-title').val(title);
        $('#modal-upload-image-bank')
            .attr('data-target-img', $(this).attr('data-target-img'))
            .attr('data-target-id', $(this).attr('data-target-id'))
            .modal();
    }
});

// $(document).on('click', '.upload-btn', function (e) {
//     e.preventDefault();
//     var target_id = $(this).attr('data-target');
//     $('#'+target_id).click();
// })

function checkImageSize(width, height, id_img, label_container){
    var msg = '';
    var tmp_size = $(id_img).attr('dimension-data');
    var size = tmp_size.split('x');
    if (size[0] == 'auto'){
        if (size[1] != height)
            msg = "Your image size is not right. You upload image with dimension <strong>"+width+"x"+height+"</strong>, image must <strong>"+tmp_size+"</strong>";
    }
    else if (size[1] == 'auto'){
        if (size[0] != width)
            msg = "Your image size is not right. You upload image with dimension <strong>"+width+"x"+height+"</strong>, image must <strong>"+tmp_size+"</strong>";
    }
    else if (size[0] != width || size[1] != height)
        msg = "Your image size is not right. You upload image with dimension <strong>"+width+"x"+height+"</strong>, image must <strong>"+tmp_size+"</strong>";
    if (label_container === undefined)
        $(id_img).parents('.image-container:eq(0)').find(".error-img:eq(0)").html(msg);
    else
        $(label_container).html(msg);
}

if ($('#modal-upload-image-bank-form').length){
    $('#modal-upload-image-bank-form').validate({
        rules : {
            photo_photographer : "required",
            photo_caption : "required",
            photo_copyright : "required",
            photo_keywords : "required",
        },
        submitHandler: function(form) {
            if ($('#modal-upload-input-file').val()){
                return true;
            }else{
                callNoty('error','Please select image before submitting upload to image bank.');
                return false;
            }
        }
    });

    var options = {
        type: 'post',
        dataType : 'json',
        beforeSubmit: function() {
            callLoader('Please wait while uploading your image to image bank.')
        },
        success: function(data) {
            endLoader();
            //check size and alert if size is wrong
            if ($('#modalUploadtoBank').attr('rel-checksize') == '1'){
                var img = new Image();
                img.onload = function() {
                  checkImageSize(this.width, this.height, '#'+$('#modal-upload-image-bank').attr('data-target-img'));
                }
                img.src = data.photo.photo_url_full;
            }
            var image = $("#"+$('#modal-upload-image-bank').attr('data-target-img'));
            image.attr("src", data.photo.photo_url_full).show();
            var next_elemet = image.next();
            if (next_elemet.is('div.img-polaroid'))
                next_elemet.hide();
            if ($('#modal-upload-image-bank').attr('data-target-img').indexOf('headline') > -1){
                headlineChanged();
            }
            image.parents('div.image-container:eq(0)').find('input[name$="crop_data"]:eq(0)').val('');
            $('#'+$('#modal-upload-image-bank').attr('data-target-id')).val(data.photo.photo_id);
            $('#modal-upload-image-bank').modal('hide');
        },
        error : function(jqXHR){
            endLoader();
            if (jqXHR.status == 444)
                sessionExpireHandler();
            else
                callNoty('warning');
        }
    };
    $('#modal-upload-image-bank-form').ajaxForm(options);
}

$(document).on('click', '.image-bank-list-btn', function (e) {
    e.preventDefault();
    var id_img   = $(this).parents('.image-container:eq(0)').attr('data-target');
    var id_input = $(this).parents('.image-container:eq(0)').attr('data-target-id');
    var keyword  = $(this).parents('.image-container:eq(0)').find('.image-bank-keyword:eq(0)').val();
    var column   = $(this).parents('.image-container:eq(0)').find('.image-bank-column:eq(0)').val();
    if (keyword.trim() == ''){
        callNoty('error', 'Please fill keyword before search image in image bank.');
        $(this).parents('.image-container:eq(0)').find('.image-bank-keyword:eq(0)').focus();
        return;
    }
    if (typeof community == "undefined" || community == null) {
       var uri = '';
    } else {
      var uri = 'exist';
    }
    $.ajax({
        url: base_url + 'image/list_image',
        data: {target_id : id_input, target_img : id_img, k : keyword, col : column, uri : uri},
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        type : 'post',
        beforeSend : function(){
            callLoader('Please wait while searching and loading image list from image bank.')
        }
    }).always(function(){
        endLoader();
    }).done(function(data){
        $('#modal-ajax-title').html('List Image <br /><small>list image from newsbank with keyword <strong>"'+keyword+'"</strong></small>');
        $('#modal-ajax-body').html(data);
        $('#modal-ajax').modal();
    }).fail(function (jqXHR, data) {
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else if (jqXHR.status == 503)
            handleSqlBusy();
        else
            callNoty('warning');
    });
});
function headlineChanged(){
    $('input[name$="crop_data"]').each(function(index, element){
        $(this).val('');
        var image = $('#'+$(this).parents('div.image-container:eq(0)').attr('data-target'));
        var crop_preview = image.next();
        if (crop_preview.is('div.img-polaroid') && crop_preview.is(':visible')){
            crop_preview.hide();
            var image_default = (image.attr('id').indexOf('thumbnail') > -1) ? holder_thumbnail : holder_secondary;
            console.log('#'+$(this).parents('div.image-container:eq(0)').attr('data-target'));
            image.show().attr('src', image_default);
            Holder.run({
                images : '#'+$(this).parents('div.image-container:eq(0)').attr('data-target')
            });
        }
    });
}
function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '')
        .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
        .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
            .join('0');
    }
    return s.join(dec);
}

//delete community
$(document).on('click', '.confirm_delete', function(e){
    e.preventDefault();
    var id = $(this).attr('id');
    swal({
    title: "Delete Reason!",
    text: "Write your delete reason",
    type: "input",
    showCancelButton: true,
    closeOnConfirm: false,
    // animation: "slide-from-top",
    inputPlaceholder: "Write reason"
  },
  function(inputValue){
    if (inputValue === false) return false;

    if (inputValue === "") {
      swal.showInputError("You need to write reason!");
      return false
    }

    
    sweet(id,inputValue);
    // window.location = url;
  });

});

 function sweet(id,inputValue) 
{
    swal({
        title: "Are you sure?",
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
        url : base_url + route + '/delete',
        method : 'POST',
        dataType: 'JSON',
        data : {
          inputValue : inputValue,
          id : id,
        },
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        beforeSend : function(){
          callLoader();
        }
      }).always(function(){
        endLoader();
      }).done(function(data){
        swal("Deleted!",'Article has been deleted', "success");
        location.reload();
        
      }).fail(function(){
        callNoty('warning');
      });
        }
    });
}

$('.currency2').maskMoney({prefix: '', 
                        thousands: '.', 
                        decimal: ',',
                        precision: 0
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

Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};

function formatMoney(n, c, d, t) {
    var c = isNaN(c = Math.abs(c)) ? 0 : c,
      d = d == undefined ? "," : d,
      t = t == undefined ? "." : t,
      s = n < 0 ? "-" : "",
      i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
      j = (j = i.length) > 3 ? j % 3 : 0;
  
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
  };

function getNumber(txt) {
    var numb = txt.match(/\d/g);
    return numb.join("");
}