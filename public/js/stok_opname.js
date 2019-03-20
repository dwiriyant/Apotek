var d = new Date();
d.setHours(0,0,0,0);

function callbackForm(){

    // set form validation rules
    var edit = $('#form-id').val() ? true : false ;

    Holder.run({
        images : '#form-photo'
    });
}

function updateStok(id_obat, stok_nyata) {
    $.ajax({
        url : base_url + 'stok-opname/remote',
        method : 'post',
        data : {
            action : 'updateStok',
            id_obat : id_obat,
            stok_nyata : stok_nyata
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
        $("#stok-nyata-note-"+id_obat).hide();
        $("#stok-software-"+id_obat).text(stok_nyata);
    }).fail(function(jqXHR, textStatus, errorThrown){
        if (jqXHR.status == 444)
            sessionExpireHandler();
        else
            callNoty('warning');
    });
}

$(function() {
    Holder.run();
    callbackForm();

    $('.stok-nyata').on('keypress',function(e) {
        var id_obat = $(this).data('id');
        var stok_nyata = $('#stok-nyata-'+id_obat).val();
        $("#stok-nyata-note-"+id_obat).show();
        if(e.which == 13 && stok_nyata != '') {
            updateStok(id_obat, stok_nyata);
        }
    });

    $(document).on('click', '.button-edit, .button-reset', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        ajaxLoadForm(url, callbackForm);
    });

})