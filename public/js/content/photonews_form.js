function setDFP() {
    var cat = $('#news-category').val();
    $('#category-interest').val(category_dfp[cat]);
}
Dropzone.autoDiscover = false;
var image_uploaded = [];

function addPhotonews(data){
    var html =  '<div class="box box-success">'
        +'<input type="hidden" name="photonews_photo_id[]" value="'+data.id+'"/>'
        +'<input type="hidden" name="photonews_id[]" value=""/>'
        +'<input type="hidden" name="photonews_realpath[]" value="'+data.path+'"/>'
        +'<div class="box-header no-border">'
            +'<div class="box-tools pull-right">'
                +'<button class="btn btn-box-tool photonews-remove"><i class="fa fa-times"></i></button>'
            +'</div><!-- /.box-tools -->'
        +'</div><!-- /.box-header -->'
        +'<div class="box-body">'
            +'<div class="row">'
                +'<div class="col-lg-4">'
                    +'<img src="'+data.url+'" class="photonews-thumb img-polaroid">'
                    +'<center>'
                        +'<button class="btn btn-default set-headline" type="button">Set as Main Image</button>'
                    +'</center>'
                +'</div>'
                +'<div class="col-lg-8">'
                    +'<div class="form-group">'
                        +'<label for="keyword1" class="control-label">Keywords</label>'
                        +'<input type="text" id="keyword1" class="form-control" name="photonews_title[]" value="" placeholder="Keywords">'
                    +'</div>'
                    +'<div class="form-group">'
                        +'<label for="copyright1" class="control-label">Copyright</label>'
                        +'<input type="text" id="copyright1" class="form-control" name="photonews_copyright[]" value="'+data.copyright+'" placeholder="Copyright">'
                    +'</div>'
                    +'<div class="form-group">'
                        +'<label for="description1" class="control-label">Description</label>'
                        +'<textarea rows="3" id="description1" class="form-control" name="photonews_credit_source[]" placeholder="Description"></textarea>'
                    +'</div>'
                +'</div>'
            +'</div>'
        +'</div>'
    +'</div>';
    $('#list-photo').append(html);
}

$(function(){
    
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var wrapper2         = $(".input_fields_wrap2"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    
    // var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        // if(x < max_fields){ //max input box allowed
            // x++; //text box increment
            $(wrapper).append('<div class="col-md-12">                                <div class="form-group clearfix col-lg-11">'
                                    +'<input type="text"  class="form-control input-sm" name="source[]">'
                                + '</div>'
                                + '<span class="btn btn-danger btn-xs col-md-1 remove_field">'
                                    + '<i class="fa fa-minus " aria-hidden="true"></i>'
                                + '</span>'
                            + '</div>');
        // } 
    });

    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); //x--;
    }); 
    $(wrapper2).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); //x--;
    });  


    if (!$('#category-interest').val())
        setDFP();

    $('#news-category').change(function (e) {
        setDFP();
    });

    $(document).on('click', '.set-headline', function(){
        $('#headline-photo').attr('src', $(this).parents('div:eq(0)').find('img:eq(0)').attr('src'));
        $('#headline-photo-id').val($(this).parents('div.box:eq(0)').find('input[name="photonews_photo_id[]"]:eq(0)').val());
        headlineChanged();
    });

    $('.lvl1').on('ifChanged', function(e){
      console.log("lalala");
    });

    $('#coba2').on('change', function(event){
      if($('#coba2').val()=='8') {
        $("#reject").show();
        $("#reject").prop("required");
        $("#schedule").hide();
        $("#feedback").hide();
      } else if ($('#coba2').val()=='2') 
      {
        $("#schedule").show();
        $("#schedule").prop("required");
        $("#feedback").show();
        $("#reject").hide();
      } 
      else {
        $("#feedback").hide();
        $("#reject").hide();
        $("#schedule").hide();
      }
      
    });
    if($('#coba2').val()=='8') {
        $("#reject").show();
        $("#reject").prop("required");
        $("#schedule").hide();
        $("#feedback").hide();
      } else if ($('#coba2').val()=='2') 
      {
        $("#schedule").show();
        $("#schedule").prop("required");
        $("#feedback").show();
        $("#reject").hide();
      } 
      else {
        $("#feedback").hide();
        $("#reject").hide();
        $("#schedule").hide();
      }


    var validator = '';

    if($('#coba2').val()!='8') {
        var validator = $("#news-form").submit(function () {
        // update underlying textarea before submit validation
        tinyMCE.triggerSave();
        }).validate({
            ignore: "",
            rules: {
                news_title: "required",
                news_synopsis: "required"
        },
        errorClass: 'help-block pull-left',
        errorPlacement: function (label, element) {
            // position error label after generated textarea
            label.insertAfter(element);
        },
        submitHandler:function(form){

            return true;
        }
    });
    
    } else {
        var validator = $("#news-form").submit(function () {
        // update underlying textarea before submit validation
        tinyMCE.triggerSave();
        }).validate({
            ignore: "",
            rules: {
                news_title: "required",
                news_synopsis: "required",
                alasan_reject : "required"
        },
        errorClass: 'help-block pull-left',
        errorPlacement: function (label, element) {
            // position error label after generated textarea
            label.insertAfter(element);
        },
        submitHandler:function(form){

            return true;
        }
    });
    }


    $('#coba2').on('change', function(event){
    if($('#coba2').val()=='8') {
    var validator = $("#news-form").submit(function () {
        // update underlying textarea before submit validation
        tinyMCE.triggerSave();
        }).validate({
            ignore: "",
            rules: {
                news_title: "required",
                news_synopsis: "required",
                alasan_reject : "required"
        },
        errorClass: 'help-block pull-left',
        errorPlacement: function (label, element) {
            // position error label after generated textarea
            label.insertAfter(element);
        },
        submitHandler:function(form){

            return true;
        }
    });
    } 
    });

    validator.focusInvalid = function () {
        // put focus on tinymce on submit validation
        if (this.settings.focusInvalid) {
            try {
                var toFocus = $(this.findLastActive() || this.errorList.length && this.errorList[0].element || []);
                if (toFocus.is("textarea#news-content")) {
                    tinyMCE.get(toFocus.attr("id")).focus();
                    console.log();
                    $('body').animate({
                        scrollTop: $("textarea#news-content").parents('.form-group:eq(0)').offset().top
                    }, 1000);
                } else {
                    toFocus.filter(":visible").focus();
                }
            } catch (e) {
                // ignore IE throwing errors when focusing hidden elements
            }
        }
    };

    $('#add-photo-uploaded').click(function(e){
        e.preventDefault();
        if (!$('#news-title').val().trim()){
            callNoty('error', 'Please fill your <strong>NEWS TITLE</strong> before saving uploaded image.');
            $('#news-title').focus();
            return;
        }
        if (image_uploaded.length <= 0){
            callNoty('error','Please upload at least 1 image before add it to this photonews.');
            return;
        }
        var valid = true;
        if (!$('#photonews-caption').val().trim()){
            $('#photonews-caption')
                .parents('.form-group:eq(0)')
                .addClass('has-error')
                .find('.help-block:eq(0)')
                .text('Caption is required');
            $('#photonews-caption').focus();
            valid = false;
        }else{
            $('#photonews-caption')
                .parents('.form-group:eq(0)')
                .removeClass('has-error')
                .find('.help-block:eq(0)')
                .text('');
        }
        if (!$('#photonews-photographer').val().trim()){
            $('#photonews-photographer')
                .parents('.form-group:eq(0)')
                .addClass('has-error')
                .find('.help-block:eq(0)')
                .text('Photographer is required');
            $('#photonews-photographer').focus();
            valid = false;
        }else{
            $('#photonews-photographer')
                .parents('.form-group:eq(0)')
                .removeClass('has-error')
                .find('.help-block:eq(0)')
                .text('');
        }

        if (valid){
            var post_data = {
                photo_title : $('#news-title').val(),
                photo_photographer : $('#photonews-photographer').val(),
                photo_caption : $('#photonews-caption').val(),
                images : image_uploaded,
                uri : 'exist'
            };
            $.ajax({
                url : base_url+"image/upload-multiple",
                data: post_data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type : 'post',
                dataType : 'json',
                beforeSend : function(){
                    $('#upload-overlay').show();
                }
            }).always(function(){
                $('#upload-overlay').hide();
            }).done(function(data){
                if (data.length){
                    $.each(data, function(index, element){
                        addPhotonews({
                            'id' : element.photo_id,
                            'path' : element.photo_path,
                            'url' : element.photo_url_full,
                            'copyright' : element.photo_copyright
                        })
                    })
                }
            }).fail(function (jqXHR) {
                if (jqXHR.status == 444)
                    sessionExpireHandler();
                else
                    callNoty('warning');
            });
        }
    });

    if ($('#news-title').length) {
        $('#news-title').simplyCountable({
            counter: '#title-counter',
            countType: 'characters',
            maxCount: 70,
            strictMax: true,
            countDirection: 'down',
        });
    }

    if ($('#news-synopsis').length) {
        $('#news-synopsis').simplyCountable({
            counter: '#synopsis-counter',
            countType: 'characters',
            maxCount: 145,
            strictMax: true,
            countDirection: 'down',
        });
    }

    $('#news-tag').magicSuggest({
        data: base_url + route +'/remote',
        hideTrigger: true,
        placeholder: 'Type & choose tags',
        useZebraStyle: true,
        allowFreeEntries: true,
        ajaxConfig: {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type : 'post',
            error : function(jqXHR){
                if (jqXHR.status == 444)
                    sessionExpireHandler();
            }
        },
        dataUrlParams : {'action' : 'get-tags'}
    });
    if (typeof tags != 'undefined' && tags.length)
            $('#news-tag').magicSuggest().setSelection(tags);

    $('input[type="checkbox"], input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });

    $('.date').datetimepicker({
        format: "ddd, D MMM YYYY HH:mm",
        showTodayButton : true,
        showClear : true,
        useCurrent : false,
        allowInputToggle : true,
    });

    $(".date").on("dp.change", function (e) {
        var value = '';
        if (e.date)
            value = e.date.format('YYYY-MM-DD HH:mm:ss');
        var obj = $(this).parents('.form-group:eq(0)').find('input.dt-value:eq(0)');
        obj.val(value);
    });

    $(document).on('ifChanged', '#news-headtorial',function () {
        if ($(this).is(":checked"))
            $('#headtorial-container').show();
        else
            $('#headtorial-container').hide();
    })

    Holder.run();

    var str_embed_script = embed_script ? 'infografis' : '';

    tinymce.init({
        selector: "textarea#news-content",
        menubar: "edit insert format table tools",
        convert_urls: false,
        keep_styles : false,
        content_css : assets_url + 'css/tinymce_content.css',
        fontsize_formats: "8px 9px 10px 11px 12px 13px 14px 16px 18px 20px 22px 26px 36px",
        plugins: [
            "advlist autolink lists charmap hr "+str_embed_script+" image link crosslink template media",
            "searchreplace wordcount visualblocks visualchars code",
            "save table contextmenu directionality emoticons paste textcolor searchreplace wordcount pagebreak fullscreen embedscript textcolor colorpicker"
        ],
        extended_valid_elements : "script[language|type|async|defer|src],a[href|title|target|onclick|rel|class|id]",
        toolbar1: "styleselect | fontsizeselect bold italic | alignleft aligncenter alignright alignjustify | bullist numlist",
        toolbar2: "outdent indent  | link crosslink | embedscript fullscreen| forecolor ",
        height: 315
    });

    $('#spell_check').click(function () {
        tinyMCE.triggerSave();
        var form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", base_url + route + "/spell/");

        // setting form target to a window named 'formresult'
        form.setAttribute("target", "spellresult");

        var titleField = document.createElement("input");
        titleField.setAttribute("type", "hidden");
        titleField.setAttribute("name", "news_title");
        titleField.setAttribute("value", $('#news-title').val());
        form.appendChild(titleField);

        var leadField = document.createElement("input");
        leadField.setAttribute("type", "hidden");
        leadField.setAttribute("name", "news_synopsis");
        leadField.setAttribute("value", $('#news-synopsis').val());
        form.appendChild(leadField);

        // var contentField = document.createElement("input");
        // contentField.setAttribute("type", "hidden");
        // contentField.setAttribute("name", "news_content");
        // contentField.setAttribute("value", $('#news-content').val());
        // form.appendChild(contentField);
        document.body.appendChild(form);

        // creating the 'formresult' window with custom features prior to submitting the form
        window.open("", 'spellresult', 'scrollbars=no,menubar=no,height=600,width=800,resizable=yes,toolbar=no,status=no');

        form.submit();
    });

    $(document).on('click','.btn-upload-local', function(){
        $(this).parents('div.choose-local:eq(0)').find('input[type="file"]:eq(0)').click();
    });

    $(document).on('keypress', '#photo-keyword', function (e) {
        var key = e.which;
        if(key == 13)  // the enter key code
        {
            $('#show-list').click();
            e.preventDefault();
        }
    })

    $('#show-list').click(function (e) {
        e.preventDefault();
        var keyword = $('#photo-keyword').val();
        var column = $('#photo-column').val();
        if (!keyword.trim()){
            callNoty('error', 'Please fill keyword before searching image.');
            $('#photo-keyword').focus();
            return false;
        }
        var btn = $(this);
        var prev_html = btn.html();
        $.ajax({
            url: base_url + 'image/list_image',
            data: {multi_select : true, k : keyword, col : column, uri : 'exist'},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type : 'post',
            beforeSend : function(){
                btn.html("<i fa fa-refresh fa-spin></i> Loading images...")
                   .prop('disabled', true);
            }
        }).always(function(){
            btn.html(prev_html)
               .prop('disabled', false);
        }).done(function(data){
            $('#list-image-result').html(data);
        }).fail(function (jqXHR) {
            if (jqXHR.status === 444)
                sessionExpireHandler();
            else if (jqXHR.status === 503){
                handleSqlBusy();
            }
            else
                callNoty('warning');
        });
    });

    $(document).on('click', '.photonews-remove', function(e){
        e.preventDefault();
        $(this).parents('div.box:eq(0)').remove();
    });

    $(document).on('click', '.pick-image-multi', function(e){
        e.preventDefault();
        if ($(this).hasClass('selected')){
            $(this).removeClass('selected')
                .removeClass('btn-danger')
                .text('Select')
                .addClass('btn-success')
                .parents('.box:eq(0)')
                .removeClass('bg-green')
                .removeClass('box-success')
                .addClass('box-default');
        }else{
            $(this).addClass('selected')
                .removeClass('btn-success')
                .text('Deselect')
                .addClass('btn-danger')
                .parents('.box:eq(0)')
                .addClass('bg-green')
                .addClass('box-success')
                .removeClass('box-default');
        }
    });

    $('#add-photo-imagebank').click(function(e){
        e.preventDefault();
        if ($('#list-image-result button.selected').length){
            $('#list-image-result button.selected').each(function(index, element){
                addPhotonews({
                    'id' : $(this).attr('data-id'),
                    'path' : $(this).attr('data-path'),
                    'url' : $(this).attr('data-url'),
                    'copyright' : $(this).attr('data-image-copyright')
                });
            });
        }else
            callNoty('error', 'Please select at least one of image before add to this news.');
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
});