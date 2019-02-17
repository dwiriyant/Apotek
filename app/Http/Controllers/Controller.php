<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Libraries\Upload;
use App\Photos;
use Valitron\Validator as Validator;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function validator($data)
    {
        return new Validator($data);
    }

    protected function _uploadPhoto($photo_id, $target_dir, $current_image, $image_name, $index_size,$community = false,$arrSizeSocmed = false)
    {
        $this->upload = new Upload();


        $photo_row = Photos::where('photo_id', (int)$photo_id)
        ->first();
        
        $_url                  = config('url_dir.image_dir');

        if ($photo_row) {
            $photo_row  = $photo_row->toArray();
            $source_img =  $_url . str_replace("-", "/", substr($photo_row['photo_entry'], 0, 10)) . '/' . $photo_row['photo_id'] . '/' . $photo_row['photo_url'];
            $this->upload->createDir($target_dir);
            $order_rev = '';
            if ($current_image != '') {
                $current_image = explode('/', $current_image);
                $current_image = end($current_image);
                $temp_ext      = $this->upload->getExtension($current_image);
                $arrTemp       = explode("-", str_replace("." . $temp_ext, "", $current_image));
                $end           = (preg_match('/^rev(\d)+/', end($arrTemp))) ? end($arrTemp) : '';
                $order_rev     = str_replace("rev", "", $end);
                $order_rev     = "-rev" . ((int)$order_rev + 1);
            }
            $ext            = $this->upload->getExtension($source_img);
            $target_imgName = $image_name . $order_rev;

            if(isset($this->_sizes[$index_size]['ratio_size']) && is_array($this->_sizes[$index_size]['ratio_size']))
                $arrSize = $this->_sizes[$index_size]['ratio_size'];
            else
                if($arrSizeSocmed)
                    $arrSize = $arrSizeSocmed;
                else
                    $arrSize = array();

            // $arrSize = (isset($this->_sizes[$index_size]['ratio_size']) && is_array($this->_sizes[$index_size]['ratio_size'])) ? $this->_sizes[$index_size]['ratio_size'] : $arrSizeSocmed ? $arrSizeSocmed : array();
            
            $return  = $this->upload->copyAndResize($source_img, $target_imgName, $target_dir, $arrSize,[],true);
            
            return $return;
        }

        return false;
    }

    protected function _uploadLocal($file, $data,$community = false,$custom_domain = '')
    { 
        if($custom_domain!='')
            $this->_active_domain = $custom_domain;
        $this->upload = new Upload();
        
        $photos                = new Photos();

        $_url                  = config('url_dir.image_url');
        $_dir                  = config('url_dir.image_dir');
        
        $photos->photo_id           = Photos::max('photo_id') +1;
        $photos->photo_entry        = date('Y-m-d H:i:s');
        $photos->photo_last_update  = $photos->photo_entry;
        $photos->photo_date         = isset($data['photo_date']) ? $data['photo_date'] : date('Y-m-d H:i:s');
        $photos->photo_title        = isset($data['photo_title']) ? $data['photo_title'] : '';
        $photos->photo_event        = isset($data['photo_event']) ? $data['photo_event'] : '';
        $photos->photo_location     = isset($data['photo_location']) ? $data['photo_location'] : '';
        $photos->photo_user         = isset($data['photo_user']) ? $data['photo_user'] : Auth::user()->name;
        $photos->photo_copyright    = isset($data['photo_copyright']) ? $data['photo_copyright'] : 'Jokowi.link';
        $photos->photo_status       = isset($data['photo_status']) ? $data['photo_status'] : 1;
        $photos->photo_photographer = isset($data['photo_photographer']) ? $data['photo_photographer'] : '';
        $photos->photo_keywords     = isset($data['photo_keywords']) ? $data['photo_keywords'] : '';
        $photos->photo_url          = isset($data['photo_url']) ? $data['photo_url'] : url_title(@$data['photo_title']);
        $photos->photo_domain_id    = isset($data['photo_domain_id']) ? $data['photo_domain_id'] : 9;
        $photos->photo_valid        = 0;
        $photos->photo_figure       = isset($data['photo_figure']) ? $data['photo_figure'] : '';
        $photos->photo_path         = isset($data['photo_path']) ? $data['photo_path'] : '';
        $photos->photo_width        = isset($data['photo_width']) ? $data['photo_width'] : 0;
        $photos->photo_height       = isset($data['photo_height']) ? $data['photo_height'] : 0;

        $explode       = explode(' ', $photos->photo_date);
        $explode2      = explode('-', $explode[0]);
        $metadata_conf = array(
            'title'                  => $photos->photo_title,
            'author'                 => $photos->photo_user,
            'authorsposition'        => "",
            'caption'                => $photos->photo_event,
            'captionwriter'          => $photos->photo_user,
            'jobname'                => "",
            'copyrightstatus'        => "Copyrighted Work",
            'copyrightnotice'        => $photos->photo_copyright,
            'ownerurl'               => 'Jokowi.link',
            'keywords'               => explode("\n", $photos->photo_keywords),
            'category'               => "",
            'supplementalcategories' => array(),
            'date'                   => $explode2[2] . '-' . $explode2[1] . '-' . $explode2[0],
            'city'                   => $photos->photo_location,
            'state'                  => $photos->photo_location,
            'country'                => $photos->photo_location,
            'credit'                 => $photos->photo_user,
            'source'                 => $photos->photo_user,
            'headline'               => $photos->photo_title,
            'instructions'           => "",
            'transmissionreference'  => "",
            'urgency'                => "5"
        );

        $saveDir = $_dir . str_replace('-', '/', substr($photos->photo_entry, 0, 10)) . '/' . $photos->photo_id;
        
        $arrSize = ["200:auto" => "200xauto"];
        if (is_array($file))
            $file = $file['tmp_name'];
        $upload = $this->upload->copyAndResize($file, $photos->photo_url, $saveDir, $arrSize, $metadata_conf);
        
        if ($upload){
            $image_size               = $this->upload->get_image_size($upload['path'] . $upload['nama']);
            
            $photos->photo_path        = str_replace(config('url_dir.image_dir'), '', $upload['path']);  
           
            $photos->photo_width       = $image_size['width'];
            $photos->photo_height      = $image_size['height'];
            $photos->photo_url         = $upload['nama'];
            $photos->photo_valid       = 1;
            $photos->photo_last_update = date('Y-m-d H:i:s');

            $photo_url = [
                'photo_url_full'          => $_url  . str_replace('-', '/', substr($photos->photo_entry, 0, 10)) . '/' . $photos->photo_id . '/' . $photos->photo_url,
                'photo_url_full_max_1000' => $_url  . str_replace('-', '/', substr($photos->photo_entry, 0, 10)) . '/' . $photos->photo_id . '/' . $photos->photo_url
            ];

            // for news content, maximum width is 1000xauto
            if ($image_size['width'] > 1000){
                $ext = $this->upload->getExtension($upload['path'] . $upload['nama']);
                if ($ext != 'gif'){
                    $resize = ['1000:auto' => '1000xauto'];
                    foreach ( $resize as $key => $size ) {
                        $crop = strpos($key, ":") ? $key : '';
                        $this->upload->resize_image($upload['path'] . $upload['nama'], $crop, $size, false);
                    }
                    $photo_url['photo_url_full_max_1000'] = $_url  . str_replace('-', '/', substr($photos->photo_entry, 0, 10)) . '/' . $photos->photo_id . '/1000xauto-' . $photos->photo_url;
                }
            }
            // end additional size for image content
            
            
            $photos->photo_id = (int) $photos->photo_id;
            $photos->photo_entry_timestamps = (int) strtotime($photos->photo_entry);
            $photos->photo_date_timestamps  = (int) strtotime($photos->photo_date);
            
            $photos->save();

            $photo = array_merge($photos->toArray(), $photo_url);
        }else
            $photo = $photos->toArray();

        return $photo;
    }

}
