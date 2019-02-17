$(function(){
    var photodropzone = new Dropzone('#upload-list-cont',{
        url: base_url + "image/tmp-upload",
        maxFilesize: 2,
        addRemoveLinks: true,
        acceptedFiles: "image/*, .webm, .mp4, .ogv, .3gp, .flv, .avi, .mov",
        accept: function(file, done) {
            if (file.type == "image/jpeg" || file.type == "image/jpg" || file.type == "image/png" || file.type == "image/gif" || file.type == "video/webm" || file.type == "video/mp4" || file.type == "video/ogv" || file.type == "video/3gp" || file.type == "video/flv" || file.type == "video/avi" || file.type == "video/quicktime") {
                done();
            } else {
                done("Error! Files of this type are not accepted");
            }
        },
        init: function() { /* event listeners for removed files from dropzone*/
            this.on("removedfile", function(file) {
                if (file.new_name != undefined && file.new_name != ''){
                    var index = image_uploaded.indexOf(file.new_name);
                    if (index > -1) {
                        image_uploaded.splice(index, 1);
                        $.ajax({
                            type: 'POST',
                            url: base_url + '/image/remove-tmp',
                            data: 'file=' + file.new_name,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                        });
                    }
                }
            });
            this.on('processing', function(file){
                $('#add-photo-uploaded').prop('disabled', true);
                return true;
            });
            this.on('queuecomplete', function(){
                $('#add-photo-uploaded').prop('disabled', false);
                return true;
            });
            this.on('success',function(file, result){
                for(i=0;i<this.files.length;i++){
                    if (this.files[i].xhr != undefined && this.files[i].xhr.response != undefined && this.files[i].xhr.response != ''){
                        var response = $.parseJSON(this.files[i].xhr.response);
                        this.files[i].new_name = response.filename;
                    }
                }
                if (typeof result == 'string')
                    result = $.parseJSON(result);
                if (result.success){
                    image_uploaded.push(result.filename);
                }
                return true;
            });
        }
    });
})