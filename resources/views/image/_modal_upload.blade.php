<?php
$img = 'holder.js/'.@$image_size.'/?text='.@$image_size.'&auto=yes';

?>

<script type="text/javascript">
    var default_copyright = '<?=$copyright?>';
    var default_upload_image_src = '<?=$img?>';
</script>

<div id="modal-upload-image-bank" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" id="modal-upload-image-bank-form" action="<?=url('image/upload_image')?>" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Upload to Image Bank</h4>
                </div>
                <div class="modal-body image-container">
                    <input type="hidden" name="photo_title" id="modal-upload-input-title" />
                    <input type="hidden" name="route" value="<?=@$route?>" />
                    <input type="file" name="photo_image" id="modal-upload-input-file" onchange="readURLToImg(this, '#modal-upload-img');" style="display:none;" />

                    <center>
                        <div style="margin-bottom: 10px;">
                        <img src="<?=$img?>" data-src="<?=$img?>" dimension-data="<?=@$image_size?>" class="img-polaroid input-block-level" id="modal-upload-img" style="width:100%;max-width: 450px;"/>
                        <br /><small class="text-danger error-img"></small>
                        </div>
                        <label for="modal-upload-input-file" class="btn btn-sm btn-primary"><i class="fa fa-file-image-o"></i> Select from local image</label>
                    </center>
                    <br />
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="modal-upload-photo-photographer" class="control-label">Photographer</label>
                                <input type="text" name="photo_photographer" id="modal-upload-photo-photographer" class="form-control" required="required" placeholder="Photographer" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="modal-upload-photo-copyright" class="control-label">Copyright</label>
                                <input type="text" name="photo_copyright" id="modal-upload-photo-copyright" class="form-control" required="required" placeholder="Copyright" value="<?=$copyright?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="modal-upload-photo-caption" class="control-label">Caption</label>
                                <input type="text" name="photo_caption" id="modal-upload-photo-caption" class="form-control" required="required" placeholder="Caption" value="" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                            <label for="modal-upload-photo-keyword" class="control-label">Keywords</label>
                            <textarea cols="30" rows="3" name="photo_keywords" id="modal-upload-photo-keyword" class="form-control" placeholder="Keywords" value=""></textarea>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fa fa-upload"></i> Upload Image</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
