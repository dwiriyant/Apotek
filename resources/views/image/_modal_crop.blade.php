<div class="modal fade" id="cropper-modal">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Crop Image</h4>
        </div>
        <div class="modal-body">
            <?php
            if (!isset($ratio)){
            ?>
            <div id="cropper" class="form-inline">
              <img src="" alt="Picture" class="img-responsive img-polaroid"><br />
              <strong>Cropped image size : </strong>
              <input type="text" disabled class="form-control input-sm" style="width:50px;" id="crop-width"> x <input disabled type="text" style="width:50px;" class="form-control input-sm" id="crop-height"></span>
            </div>
            <?php }else{?>
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="ratio" class="control-label">Ratio</label>
                        <select id="crop-ratio" class="form-control">
                            <option value="free">Free</option>
                            <option value="1:1">1:1</option>
                            <option value="1:2">1:2</option>
                            <option value="2:1">2:1</option>
                            <option value="2:3">2:3</option>
                            <option value="3:2">3:2</option>
                            <option value="4:3">4:3</option>
                            <option value="3:4">3:4</option>
                            <option value="16:9">16:9</option>
                            <option value="9:16">9:16</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div id="cropper-loader" class="text-center"><i class="fa fa-spin fa-refresh"></i> Please wait while loading your image and preparing cropper area.</div>
                    <div id="cropper">
                      <img src="" alt="Picture" class="img-responsive img-polaroid">
                    </div>
                    <small class="help-block text-center">HOLD LEFT CLICK to MOVE background OR visible area.<br />Use MOUSE SCROLL to ZOOM image.</small>
                </div>
            </div>
            <?php }?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="apply-crop"><i class="fa fa-check"></i> Apply</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>