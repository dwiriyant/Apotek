function setDFP() {
    var cat = $('#news-category').val();
    $('#category-interest').val(category_dfp[cat]);
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
                news_synopsis: "required",
                news_content: "required"
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
                news_content: "required",
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
        $('#alasan-reject').rules('add',{required:true});

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
        showClear : true,
        showTodayButton : true,
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
        keep_styles : false,
        convert_urls: false,
        content_css : assets_url + 'css/tinymce_content.css',
        fontsize_formats: "8px 9px 10px 11px 12px 13px 14px 16px 18px 20px 22px 26px 36px",
        plugins: [
            "advlist autolink lists charmap hr "+str_embed_script+" image link crosslink media",
            "searchreplace wordcount visualblocks visualchars code",
            "save table contextmenu directionality emoticons paste textcolor searchreplace wordcount pagebreak fullscreen embedscript textcolor colorpicker"
        ],
        extended_valid_elements : "script[language|type|async|defer|src],a[href|title|target|onclick|rel|class|id]",
        pagebreak_separator : "<!-- splitter content -->",
        toolbar1: "styleselect | fontsizeselect bold italic | alignleft aligncenter alignright alignjustify | bullist numlist",
        toolbar2: "outdent indent  | image link | crosslink pagebreak | embedscript fullscreen | forecolor ",
        height: 315
    });

    if ($('#quote-container').length){
        $(document).on('click','#add-quote', function(e){
            e.preventDefault();
            var html_quote = '<div class="quote-item">'
                        +'<input type="hidden" name="quote_id[]" value="">'
                        +'<div class="form-group">'
                            +'<label for="quote-figure-1" class="form-label col-lg-3 col-md-4">Figure</label>'
                            +'<div class="col-lg-9 col-md-8">'
                                +'<div class="input-group input-group-sm">'
                                    +'<input type="text" name="quote_figure[]" id="quote-figure-1" class="form-control" value="" placeholder="Figure">'
                                    +'<span class="input-group-btn">'
                                        +'<button class="btn btn-danger remove-quote" type="button"><i class="fa fa-remove"></i></button>'
                                    +'</span>'
                                +'</div>'
                            +'</div>'
                        +'</div>'
                        +'<div class="form-group">'
                            +'<label for="quote-content-2" class="form-label col-lg-3 col-md-4">Content</label>'
                            +'<div class="col-lg-9 col-md-8">'
                                +'<textarea name="quote_content[]" rows="4" id="quote-figure-1" class="form-control" placeholder="Content"></textarea>'
                            +'</div>'
                        +'</div>'
                    +'<hr>'
                    +'</div>';
            $('#quote-container').append(html_quote);
        });

        $(document).on('click', '.remove-quote', function (e) {
            e.preventDefault();
            $(this).parents('.quote-item:eq(0)').remove();
        });
    }

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

        var contentField = document.createElement("input");
        contentField.setAttribute("type", "hidden");
        contentField.setAttribute("name", "news_content");
        contentField.setAttribute("value", $('#news-content').val());
        form.appendChild(contentField);
        document.body.appendChild(form);

        // creating the 'formresult' window with custom features prior to submitting the form
        window.open("", 'spellresult', 'scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,status=no');

        form.submit();
    });

    $(document).on('click','.btn-upload-local', function(){
        $(this).parents('div.choose-local:eq(0)').find('input[type="file"]:eq(0)').click();
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

})