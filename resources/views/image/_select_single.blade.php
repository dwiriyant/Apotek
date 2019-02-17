<?php
if($coll == "community_photos"){
        $dir = 'community/';
        $uri = 'exist';
        $src = $this->config['cdn_img_200xauto'];
        $image_size = '';

    } else {
        $dir = '';
        $uri = '';
        $src = $this->config['klimg_url'].'real/';
        $image_size .= '-';
    }
if (count($photos)){
    $i = 0;
    foreach ($photos as $photo){
        if ($i % 3 == 0)
            echo '<div class="row">';
        echo '<div class="col-lg-4 col-md-4 col-sm-4">
              <div class="box box box-success">
                    <div class="box-body">
                    <div class="photo-container">
                        <img src="'.$src.str_replace('-', '/', substr($photo['photo_entry'], 0, 10)).'/'.$photo['photo_id'].'/'.$image_size.$photo['photo_url'].'" style="width:100%;max-height:95%;">
                    </div>
                    <div style="text-align:center;">
                        <div class="text-muted" style="font-weight:bold; font-size: 12px;">'.$photo['photo_width'].'x'.$photo['photo_height'].'</div>
                        <button type="button" class="btn btn-small btn-success pick-image-single" data-image-width="'.$photo['photo_width'].'" data-image-title="'.$photo['photo_title'].' '.$photo['photo_copyright'].'" data-id="'.$photo['photo_id'].'" data-url="'.$this->config['klimg_url'].$dir.$photo['photo_path'].$photo['photo_url'].'"><i class="fa fa-plus"></i> Select</button>
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
}else{
    echo "Sorry, there's no image that you've searching for.";
    if ($page > 1)
        echo '<div class="row">
            <div class="col-lg-12">
                <div class="clearfix modal-ajax-pagination">
                    <ul class="pagination pull-right">
                        <li class="paginate_button '.($page <= 1 ? 'active' : '' ).'">
                            <a href="'.($page > 1 ? url('image/list_image/', ['page' => ($page-1),'uri' => $uri]) : '#' ).'" class="link_next btn">Prev</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>';
}
?>
<script type="text/javascript">
    $('.modal-ajax-pagination > .pagination > li.paginate_button > a').click(function(e){
        e.preventDefault();
        var obj = $(this);
        var html = $(this).html();
        $.ajax({
            url : obj.attr('href'),
            type : 'post',
            dataType : 'html',
            data : {k : '<?= str_replace("'", "\'", htmlEncode($k))?>', col : '<?= str_replace("'", "\'", htmlEncode($col))?>', single_select : true},
            beforeSend : function(){
                obj.html("<i class='fa fa-refresh fa-spin'></i>");
            }
        }).done(function(data){
            $('#image-result').html(data);
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
