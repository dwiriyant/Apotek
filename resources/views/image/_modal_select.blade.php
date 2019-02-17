<div class="modal-body" style="background-color: #ECF0F5;" data-target="<?=$target_img?>" data-input-id="<?=$target_id?>">
    <?php

    $uri = 'exist';
    $size = '';
    
    $src = config('url_dir.cdn_image'). '200xauto/indonesia-maju/';

    $img_url = config('url_dir.image_url');

    if (count($photos)){
        $i = 0;
        foreach ($photos as $photo){
            if ($i % 3 == 0)
                echo '<div class="row">';
            $photographer  = $photo['photo_photographer'];
            $tmp = json_decode($photographer, true);
            if (is_array($tmp))
                $photographer = @$tmp[0]['name'];
			echo '<div class="col-lg-4 col-md-4 col-sm-4">
                <div class="box box box-success">
        			<div class="box-body">
        			<div class="photo-container">
        				<img class="popovers" data-toggle="popover" data-trigger="hover" data-html="true" data-placement="bottom" data-content="'.htmlEncode($photo['photo_event']).'" title="<strong>By</strong> : '.htmlEncode($photographer).' <br /><small style=\'font-size:11px;\'>'.htmlEncode($photo['photo_copyright']).'</small>" src="'.$src.str_replace('-', '/', substr($photo['photo_entry'], 0, 10)).'/'.$photo['photo_id'].'/'.$size.$photo['photo_url'].'" style="width:100%;max-height:95%;">
        			</div>
        			<div style="text-align:center;">
        				<div class="text-muted" style="font-weight:bold; font-size: 12px;">'.$photo['photo_width'].'x'.$photo['photo_height'].'</div>
        				<button type="button" class="btn btn-small btn-success pick-image" data-id="'.$photo['photo_id'].'" data-url="'.$img_url.$photo['photo_path'].$photo['photo_url'].'"><i class="fa fa-plus"></i> Select</button>
        			</div>
        			</div>
		          </div>
                  </div>';
            if ($i % 3 == 2)
                echo "</div>";
            $i++;
        }
        $i--;
        if ($i % 3 == 1 || $i % 3 == 1 || $i == 0)
    	   echo "</div>";
        echo '<div class="row">
            <div class="col-lg-12">
                <div class="clearfix modal-ajax-pagination">
                    <ul class="pagination pull-right">
                        '.($page > 1
                            ? '<li class="paginate_button">
                                    <a href="'.url('image/list_image/', ['page' => ($page-1),'uri' => $uri]).'" class="link_next btn">Prev</a>
                                </li>'
                            : ''
                        ).'
                        <li class="paginate_button">
                            <a href="'.(count($photos) > 0 ? url('image/list_image/', ['page' => ($page+1),'uri' => $uri]) : '#' ).'" class="link_last btn">Next</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>';
                    /*<div class="pull-left">
                        Showing <strong>'.($offset + 1).'</strong> - <strong>'.(($offset+$limit > $total) ? $total : $offset+$limit).'</strong> of <strong>'.$total.'</strong> data
                    </div>
                    '.$pagination.'*/
    }else{
        echo "Sorry, there's no image that you've searching for.";
        if ($page > 1)
            echo '<div class="row">
                <div class="col-lg-12">
                    <div class="clearfix modal-ajax-pagination">
                        <ul class="pagination pull-right">
                            <li class="paginate_button '.($page <= 1 ? 'active' : '' ).'">
                                <a href="'.($page > 1 ? url('image/list_image/', ['page' => ($page-1)]) : '#' ).'" class="link_next btn">Prev</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>';
    }
    ?>
</div>
<script type="text/javascript">
	$('.pick-image').click(function(){
		var src        = $(this).attr("data-url");
		var photo_id   = $(this).attr("data-id");
		var img        = new Image();
		var modal_body = $(this).parents('div.modal-body:eq(0)');
        img.onload = function () {
            checkImageSize(this.width, this.height, '#'+modal_body.attr('data-target'));
        }
        img.src = src;
        var image = $("#"+modal_body.attr('data-target'));
        image.attr("src", src).show();
        var next_elemet = image.next();
        if (next_elemet.is('div.img-polaroid'))
            next_elemet.hide();
        if (modal_body.attr('data-target').indexOf('headline') > -1){
            headlineChanged();
        }
        image.parents('div.image-container:eq(0)').find('input[name$="crop_data"]:eq(0)').val('');
        $("#"+modal_body.attr('data-input-id')).val(photo_id);
        $('#modal-ajax').modal('hide');
	});
    $('.popovers').popover();
    $('.modal-ajax-pagination > .pagination > li.paginate_button > a').click(function(e){
        e.preventDefault();
        var obj = $(this);
        var html = $(this).html();
        $.ajax({
            url : obj.attr('href'),
            type : 'post',
            dataType : 'html',
            data : {k : '<?= str_replace("'", "\'", htmlEncode($k))?>', col : '<?= str_replace("'", "\'", htmlEncode($col))?>', target_id : '<?= $target_id?>', target_img : '<?= $target_img?>'},
            beforeSend : function(){
                obj.html("<i class='fa fa-refresh fa-spin'></i>");
            }
        }).done(function(data){
            obj.parents('div.modal-body:eq(0)').parent().html(data);
        }).fail(function (jqXHR) {
            if (jqXHR.status == 444)
                sessionExpireHandler();
            else{
                obj.html(html);
                callNoty('warning');
            }
        });
    })
</script>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
