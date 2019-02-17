<div id="modal-image-tinymce" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Insert Image</h4>
            </div>
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab-upload" data-toggle="tab">Upload</a></li>
              <li><a href="#tab-image-bank" data-toggle="tab">Image Bank</a></li>
              <li><a href="#tab-url" data-toggle="tab">Image URL</a></li>
            </ul>
            <div class="modal-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-upload">
                        <form method="post" id="modal-insert-form" action="<?=url('image/upload_image')?>" enctype="multipart/form-data">
                        <input type="hidden" name="route" value="<?=$route?>">
                            <input type="file" name="photo_image" id="modal-insert-file" onchange="readURLToImg(this, '#modal-insert-img');" style="display:none;" />
                            <center>
                                <div style="margin-bottom: 10px;">
                                <img src="holder.js/400x200/?text=Upload Your Image Here&auto=yes" dimension-data="0x0" data-src="holder.js/400x200/?text=Upload Your Image Here&auto=yes" class="img-polaroid input-block-level" id="modal-insert-img" style="width:100%;max-width: 450px;"/>
                                </div>
                                <a href="#" data-target="modal-insert-file" class="btn btn-sm btn-primary upload-btn"><i class="fa fa-file-image-o"></i> Select from local image</a>
                            </center>
                            <br />
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="modal-insert-title" class="control-label">Title</label>
                                        <input type="text" name="photo_title" id="modal-insert-title" class="form-control" required="required" placeholder="Title" />
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="modal-insert-photographer" class="control-label">Photographer</label>
                                        <input type="text" name="photo_photographer" id="modal-insert-photographer" class="form-control" required="required" placeholder="Photographer" />
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="modal-insert-copyright" class="control-label">Copyright</label>
                                        <input type="text" name="photo_copyright" id="modal-insert-copyright" class="form-control" required="required" placeholder="Copyright" value="<?=$copyright?>" />
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="modal-insert-caption" class="control-label">Caption</label>
                                        <input type="text" name="photo_caption" id="modal-insert-caption" class="form-control" required="required" placeholder="Caption" value="" />
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="modal-insert-keyword" class="control-label">Keywords</label>
                                        <textarea cols="30" rows="3" name="photo_keywords" id="modal-insert-keyword" class="form-control" placeholder="Keywords" value=""></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label">&nbsp;</label><br />
                                        <label ><input type="checkbox" id="modal-insert-upload-info" value="1" /> Add caption, copyright & photographer below image.</label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div><!-- /.tab-pane -->
                    <div class="tab-pane" id="tab-image-bank">
                        <div class="row">
                            <div class="col-lg-12 form-inline" >
                                <form method="post" id="form-insert-imagebank" class="form-inline" action="#">
                                    <label class="control-label" >Search Image</label>
                                    <input type="text" name="keyword" placeholder="search image here ..." class="form-control input-sm">
                                    <select class="form-control input-sm" name="column">
                                        <option value=""> -- All -- </option>
                                        <option value="photo_title" selected>Title</option>
                                        <option value="photo_event">Caption</option>
                                        <option value="photo_keyword">Keyword</option>
                                    </select>
                                    <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-list"></i> Show List</button><br />
                                    <div class="form-group" style="margin:10px 0px;">
                                        <label><input type="checkbox" id="modal-insert-bank-info" value="1" /> Add caption, copyright & photographer below image.</label>
                                    </div>
                                </form>
                            </div>
                            <hr>
                            <div class="col-lg-12" id="image-result" style="padding: 10px;background-color: #eee;">
                                No Result.
                            </div>
                        </div>
                    </div><!-- /.tab-pane -->
                    <div class="tab-pane" id="tab-url">
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="#" id="form-insert-url" class="form-horizontal">
                                    <div class="form-group">
                                        <label for="modal-insert-url-url" class="control-label col-lg-3">URL</label>
                                        <div class="col-lg-9">
                                            <input type="text" name="url" id="modal-insert-url-url" class="form-control" required="required" placeholder="URL" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="modal-insert-url-title" class="control-label col-lg-3">Title</label>
                                        <div class="col-lg-9">
                                            <input type="text" name="title" id="modal-insert-url-title" class="form-control" required="required" placeholder="Title" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="modal-insert-url-copyright" class="control-label col-lg-3">Copyright</label>
                                        <div class="col-lg-9">
                                            <input type="text" name="copyright" id="modal-insert-url-copyright" class="form-control" required="required" placeholder="Copyright" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-3">&nbsp;</label>
                                        <div class="col-lg-9">
                                            <label><input type="checkbox" id="modal-insert-link-info" value="1" /> Add title & copyright below image.</label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div><!-- /.tab-pane -->
                </div><!-- /.tab-content -->
            </div>
            <div class="modal-footer">
                <div id="tab-upload-footer">
                    <button type="button" id="modal-insert-submitter" class="btn btn-success"><i class="fa fa-upload"></i> Upload Image</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                <div id="tab-image-bank-footer" style="display:none;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                <div id="tab-url-footer" style="display:none;">
                    <button type="button" id="modal-inserter" class="btn btn-success"><i class="fa fa-image"></i> Insert</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>