<?php

namespace App\Http\Controllers;

use App\Libraries\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;
use App\Photos;
use Auth;

class ImagesController extends Controller
{
    private $_temp_dir;
    private $_allow_type = array(
        'jpeg',
        'jpg',
        'gif',
        'png'
    );

    protected $upload;
 
    public function __construct(Upload $upload) {
        $this->upload = $upload;
        $this->max_size_upload_headline    = 2;
    }

    function tmpRemove(Request $request)
    {
        unlink(config('url_dir.image_dir').'tmp/'.$request['img']);
        
        return 'sukses';
    }

    function tmpUpload(Request $request, $dir_custom='')
    {
        $real_name = request()->real;
        if($request['crop_data']){
            $crop_image= json_decode(html_entity_decode($request['crop_data']), true);
            $crop_image['file'] = str_replace(config('url_dir.image_url'), config('url_dir.image_dir'), $request['file_url']);
            $target_dir = config('url_dir.image_dir').'tmp/';
            $image = $this->_cropImage($target_dir, $crop_image);
            echo json_encode($image);
        }else{
            $this->validate($request, [
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if(!isset($_FILES['file'])){
        
                header('Content-Type: application/json');
                $ret['error'] = "file size greater than allowed maximum or Please select a file to upload.";
                echo json_encode($ret);exit();

            }

            if(empty($_FILES["file"]["tmp_name"]) || empty($_FILES["file"]["name"])){
                header('Content-Type: application/json');
                $ret['error'] = "file empty";
                echo json_encode($ret);
            }

            $target_dir = config('url_dir.image_dir');
            if ( !is_dir ( $target_dir )) mkdir ( $target_dir );

            if($dir_custom !=''){
                $target_dir .= $dir_custom;
                if ( !is_dir ( $target_dir )) mkdir ( $target_dir );
            }else{
                $target_dir .= 'tmp/';
                if ( !is_dir ( $target_dir )) mkdir ( $target_dir );
            }
            
            $imageFileType = pathinfo($_FILES["file"]["tmp_name"], PATHINFO_EXTENSION);
            $eksm = explode('.', strtolower(basename($_FILES["file"]["name"])));
            $eks = $eksm[count($eksm) - 1];
            $filename = $real_name == 'true' ? basename($_FILES["file"]["name"]) : 'image_'. str_replace(',', '-', microtime(true)) . '.' . $eks;
            $target_file = $target_dir . $filename;
            $check = getimagesize($_FILES["file"]["tmp_name"]);
            $uploadOk = 1;

            if($check !== false) 
            {
                // 1 MB
                if ($_FILES["file"]["size"] > $this->max_size_upload_headline * 1024 * 1000) 
                {
                    $ret['error'] = "Sorry, your file is too large. (max ".$this->max_size_upload_headline." MB)";
                    $uploadOk = 0;
                }
                
                // Allow certain file formats
                if($eks != "jpg" && $eks != "png" && $eks != "jpeg"
                && $eks != "gif" ) 
                {
                    $ret['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $uploadOk = 0;
                }
                
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) 
                {
                    header('Content-Type: application/json');
                    echo json_encode($ret);
                // if everything is ok, try to upload file
                } 
                else 
                {
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) 
                    {
                        header('Content-Type: application/json');
                        $ret['success']['header'] = 'Success';
                        $ret['success']['message'] = 'Your Image Has Been Uploaded.';
                        $ret['success']['filename'] = $filename;

                        echo json_encode($ret);
                    } 
                    else 
                    {
                        if(is_writable(dirname($target_file))){
                            header('Content-Type: application/json');
                            $ret['error'] = "Sorry, internal error. Please check target dir permission.";
                            echo json_encode($ret);

                        }else{
                            header('Content-Type: application/json');
                            $ret['error'] = "Sorry, there was an error uploading your file. in target_dir " . $target_file;
                            echo json_encode($ret);
                        }
                        
                    }
                }           
            }
            else
            {
                header('Content-Type: application/json');
                $ret['error'] = 'error!';
                echo json_encode($ret);
            }
        }
        
    }

    function _cropImage($target_dir, $crop_image)
    {
        if ($crop_image && is_file($crop_image['file']) && isset($crop_image['x']) && isset($crop_image['y']) && isset($crop_image['width']) && isset($crop_image['height'])) {
            $path = explode('/', $crop_image['file']);
            unset($path[count($path) - 1]);
            $file              = pathinfo($crop_image['file']);
            $ext               = $file['extension'];
            $dt['final_size']  = ($crop_image['width']) . 'x' . ($crop_image['height']);
            $dt['img_newname'] = 'image_crop_' . str_replace(',', '-', microtime(true)) . '.' . $ext;
            $dt['dest_path']   = $target_dir;
            $dt['x']           = $crop_image['x'];
            $dt['y']           = $crop_image['y'];
            $dt['w']           = $crop_image['width'];
            $dt['h']           = $crop_image['height'];
            $dt['img_real']    = $crop_image['file'];
            $ret               = $this->upload->cropImage($dt);
            return $ret;
        }
    }

    /**
     * proccess saving uploaded photo & it's data
     */
    function upload()
    {
        if (isAjax() && isPost()) {
            $file = $_FILES['photo_image'];
            
            if ($file['tmp_name'] && $file['error'] != 4) {
                $photo                       = [];
                $photo['photo_date']         = post('photo_date', date('Y-m-d H:i:s'));
                $photo['photo_title']        = post('photo_title');
                $photo['photo_event']        = post('photo_caption');
                $photo['photo_location']     = post('photo_location');
                $photo['photo_user']         = post('photo_user', Auth::user()->name);
                $photo['photo_copyright']    = htmlentities(post('photo_copyright', 'Jokowi.link'));
                $photo['photo_photographer'] = post('photo_photographer');
                $photo['photo_keywords']     = post('photo_keywords');
                $photo['photo_url']          = url_title(post('photo_title'));
                
                $community = true;    

                $photo = $this->_uploadLocal($file, $photo,$community);

                echo json_encode([
                    'success' => true,
                    'photo'   => $photo,
                ]);
            } else
                echo json_encode(['success' => false, 'message' => 'Failed upload image because you have\'t choose any image yet.']);
        } else
            abort(404);
    }

    function modalImage()
    {
        if (isAjax()) {
            $k              = post('k');
            $col            = post('col');
            $allowed_column = ['photo_event', 'photo_keywords', 'photo_title'];
            $limit          = 50;
            $page           = get('page', 1);
            $offset         = offset($page, $limit);

            $data_filter = [];
            if ($col && in_array($col, $allowed_column))
                $data_filter[$col] = ['$regex' => $k,'$options'=>'i'];
            else
            {
                $data_filter['$or'] = [['photo_title' => ['$regex' => $k,'$options'=>'i']], 
                                        ['photo_keywords' => ['$regex' => $k,'$options'=>'i']],
                                        ['photo_event' => ['$regex' => $k,'$options'=>'i']]];
            }

            $photos = Photos::whereRaw($data_filter)->take($limit)->skip($offset)->orderBy('photo_title','ASC')->get()->toArray();


            if (post('multi_select')) {
                return view('image/_multi_select', [
                    'photos'     => $photos,
                    'image_size' => '200xauto',
                    'k'          => $k,
                    'col'        => $col,
                    'page'       => $page,
                    /*'pagination' => $pagination,
                    'total'      => $total,*/
                    'offset'     => $offset,
                    'limit'      => $limit,
                ]);
            } elseif (post('single_select')) {
                return view('image/_select_single', [
                    'photos'     => $photos,
                    'image_size' => '200xauto',
                    'k'          => $k,
                    'col'        => $col,
                    'page'       => $page,
                    /*'pagination' => $pagination,
                    'total'      => $total,*/
                    'offset'     => $offset,
                    'limit'      => $limit,
                ]);
            } else {
                return view('image/_modal_select', [
                    'photos'     => $photos,
                    'target_id'  => post('target_id'),
                    'target_img' => post('target_img'),
                    'page'       => $page,
                    'image_size' => '200xauto',
                    'k'          => $k,
                    'col'        => $col,
                    /*'pagination' => $pagination,
                    'total'      => $total,*/
                    'offset'     => $offset,
                    'limit'      => $limit,
                ]);
            }
        } else
            abort(404);
    }
}