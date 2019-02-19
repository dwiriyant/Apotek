<?php
namespace App\Libraries;

use App\Libraries\CloudStorage;

class Upload {

    private $_private_log = '/data/sooperboy/logs/admin/send_to_server_failed.log';
    private $_imageType = array( 'jpeg', 'jpg', 'gif', 'png' );
     private $_videoType = array(
        "webm", 
        "mp4", 
        "ogv", 
        "3gp", 
        "flv", 
        "avi",
        "mov"
    );
    private $_fileType = array( 'doc', 'xls', 'docx', 'xlsx', 'pdf', 'zip', 'rar', 'ppt', 'pptx' );
    private $_error_code = array( 'There is no error, the file uploaded with success. ',
        'The uploaded file exceeds the upload_max_filesize directive in php.ini. ',
        'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form. ',
        'The uploaded file was only partially uploaded. ',
        'No file was uploaded. ',
        6 => 'Missing a temporary folder.',
        7 => 'Failed to write file to disk.'
    );
    private $max_size = 3072000;

    function __construct () {
        //$this->watermark_file = 'http://cdn.klimg.com/kapanlagi.com/v5/i/channel/men/MEN-logo-png.png';
        $this->watermark_file = 'http://cdn.klimg.com/kapanlagi.com/v5/i/channel/men/Logo-outerglow2.png';
        $this->log_location = $this->_private_log;
        
        // Cloud Storage
        $this->CloudStorage = new CloudStorage();
        
    }

    /* fungsi untuk mengupload file serta ada parameter untuk resize
     * parameter :  1. file = array $_FILES yang telah diupload
     *              2. nama = nama file (untuk namanya)
     *              3. type = ada dua tipe yaitu file dan foto
     *              4. subfolder = subfolder dari base upload dir
     *              5. resize = array dengan format [RASIO]=>[IMAGE SIZE] => array('1:1.5'=>''100x150', '2:1'=>'200x100', '68:40'=>'68x40')
     *
     * contoh : $this->upload($_FILES['image'],10,'image','article/banner/',array('1:1.5'=>'100x150', '2:1'=>'200x100', '68:40'=>'68x40'));
     * setiap image akan tersimpan dgn masing-masing size dengan format [lokasi folder]/[size]-[nama file]
     */

    function upload ($file, $name, $type = 'file', $sub_folder = '', $resize = array(), $addWatermark = false, $metadata_conf = array()) {
        $addWatermark = false;
        # ---------------------------------------------------------------------------------------
        #   check, is $imgTarget_name is "real" path
        # ---------------------------------------------------------------------------------------
        
        $pattern = '/' . str_replace('/', '\/', config('url_dir.image_dir2')) . 'real\/*/';

        if ( preg_match($pattern, $sub_folder) && $type == 'foto' ) {
            $resize = Config::App()->get('real_image_resize');
        }
        # ---------------------------------------------------------------------------------------

        $ext = $this->getExtension(@$file['name']);
        $replace_size = str_pad(rand(0, pow(10, 10)-1), 10, '0', STR_PAD_LEFT);
        $name = preg_replace("/^((\d+|auto)x(\d+|auto))/", $replace_size, $name);
        //echo $ext;die;

        $fileSize = @$file['size'];

        switch ( true ) {
            case (@$file['error'] > 0) : return "Error : " . $this->_error_code[$file['error']];
            case ($type == 'foto' && !in_array($ext, $this->_imageType)) : return 'tipe file tidak cocok. File harus berupa gambar.';
            case ($type == 'file' && !in_array($ext, $this->_fileType)) : return 'tipe file tidak cocok.';
        }

        if ( $type == 'foto' && !in_array($ext, $this->_imageType) ) {
            return 'tipe file tidak cocok. File harus berupa gambar.';
        }

        if ( $fileSize > $this->max_size )
            return "ukuran file gambar max 3Mb";

        #$location = ASSET_IMG_UPLOAD_DIR;
        if ( $sub_folder )
            $location = $sub_folder . '/';

        $this->createDir($location);

        if ( file_exists($location . $name . "." . $ext) )
            unlink($location . $name . "." . $ext);


        if ( !move_uploaded_file($file['tmp_name'], $location . $name . "." . $ext) ) {
            return 'upload gagal';
        }
        else {
            //set realimage metadata
            $this->optimizeImage($location.$name.'.'.$ext);
            if ( is_array($metadata_conf) && count($metadata_conf) > 0 ) {
                $metadata_conf['filename'] = $location . $name . "." . $ext;
                $ret_metadata = $this->metadata_image($metadata_conf);
                //echo '<pre>'; print_r($ret_metadata); echo '<pre>';
            }
            //end
            // cloud storage
            if ( $this->CloudStorage != null)
            {
                $object_source = $location . $name . "." . $ext;
                $this->CloudStorage->upload_object(str_replace(config('url_dir.image_dir2'), '', $object_source), $object_source);
            }
            // end cloud storage
            $ret_resize = false;
            $ret_dirresize = $ret_urlresize = array();
            if ( is_array($resize) && count($resize) && $type != 'file' ) {
                $ret_resize = array();
                foreach ( $resize as $key => $size ) {
                    $file = $location . $name . "." . $ext;
                    $crop = strpos($key, ":") ? $key : '';
                    $ret_dirresize[$size] = $this->resize_image($file, $crop, $size, $addWatermark);
                    $ret_urlresize[$size] = str_replace(MEDIA_DIR, KLIMG_URL, $ret_dirresize[$size]);
                    //set resized iamge metadata
                    if ( is_array($metadata_conf) && count($metadata_conf) > 0 ) {
                        $metadata_conf['filename'] = $ret_dirresize[$size];
                        $ret_metadata = $this->metadata_image($metadata_conf);
                        //echo '<pre>'; print_r($ret_metadata); echo '<pre>';
                    }
                    //end
                    // cloud storage
                    // if ( $this->CloudStorage != null)
                    // {
                    //     $object_source = $ret_dirresize[$size];
                    //     $this->CloudStorage->upload_object(str_replace(config('url_dir.image_dir2'), '', $object_source), $object_source);
                    // }
                    // end cloud storage
                }
            }
            //send to klimg  REMARK TEMPORARY
            $sourcefile = array_merge($ret_dirresize, array( $location . $name . "." . $ext ));
            $this->send_to_server($sourcefile);

            $return = array(
                'nama' => $name . "." . $ext,
                'type' => $ext,
                'size' => $fileSize,
                'path' => $location,
                'url_resize' => $ret_urlresize,
                'dir_resize' => $ret_dirresize,
                'sourcefile' => $sourcefile
            );

            return $return;
        }
    }

    /**
     * this function used to copy real image into another directory and then create duplicate image
     * from real image into several resized size
     * @param   $imgReal_full_path      string  full path dir url and name of the source image
     * @param   $imgTarget_name         string  target NAME of the target image (without ext)
     * @param   $imgTarget_dir          string  target DIR of the target image
     * @param   $resize                 array   array set of ratio and size image
     * Ex : $this->copyAndResize(
     *          '/data/image/source/image-source-name.jpg',
     *          'image-target-name',
     *          '/data/image/target/',
     *          array('1:1.5'=>'100x150', '2:1'=>'200x100', '68:40'=>'68x40')
     *      );
     */
    function copyAndResize ($imgReal_full_path, $imgTarget_name, $imgTarget_dir, $resize = array(), $metadata = array(),$from_cloud = false, $addWatermark = false, $asset = false) {
        
        # ---------------------------------------------------------------------------------------
        #   check, is $imgTarget_name is "real" path
        # ---------------------------------------------------------------------------------------
        
        $pattern = '/' . str_replace('/', '\/', config('url_dir.image_dir2')) . 'real\/*/';

        if ( preg_match($pattern, $imgTarget_dir) ) {
            $resize = Config::App()->get('real_image_resize');
        }
        # ---------------------------------------------------------------------------------------

        
        $ext = $this->getExtension($imgReal_full_path);
        
        if ( !in_array($ext, $this->_imageType) && !in_array($ext, $this->_videoType)) {

            return false;
        }

        if(in_array($ext, $this->_videoType))
        {
            $resize = [];
        }

        $source_img = $imgReal_full_path;
        #$target_img = $imgTarget_dir . $imgTarget_name . $ext;
        $replace_size = str_pad(rand(0, pow(10, 10)-1), 10, '0', STR_PAD_LEFT);
        $imgTarget_name = preg_replace("/^((\d+|auto)x(\d+|auto))/", $replace_size, $imgTarget_name);
        $target_img = $imgTarget_dir . '/' . $imgTarget_name . '.' . $ext; 

        //create dir
        $this->createDir($imgTarget_dir . '/');

        //echoPre($imgTarget_dir);
        
        if (!is_file($source_img) && $from_cloud==false)
            return false;

        //copy real image
        if($from_cloud==false)
            copy($source_img, $target_img) or die('Failed to copy :' . $source_img . '--to--' . $target_img);

        $this->optimizeImage($target_img);
        if ( is_array($metadata) && count($metadata) > 0 ) {
            $metadata['filename'] = $target_img;
            $ret_metadata = $this->metadata_image($metadata);
        }
        // cloud storage

        if ( $this->CloudStorage != null)
        {
            $target_img = str_replace('//', '/', $target_img);

            if($from_cloud==true)
            {
                $source_img_cloud = str_replace(config('url_dir.image_dir2'), '', $source_img);

                $this->CloudStorage->download_object($source_img_cloud,$target_img);
                $this->CloudStorage->upload_object(str_replace(config('url_dir.image_dir2'), '', $target_img), $target_img);
                unlink($target_img);
            } else
                $this->CloudStorage->upload_object(str_replace(config('url_dir.image_dir2'), '', $target_img), $target_img);

        }
        // end cloud storage     
        
        //copy resized image
        $ret_dirresize = $ret_urlresize = array();
        if ( !is_array($resize) )
            $resize = array( $resize );

        foreach ( $resize as $key => $size ) {
            $crop = strpos($key, ":") ? $key : '';
            $ret_dirresize[$size] = $this->resize_image($target_img, $crop, $size, $addWatermark);
            //$ret_urlresize[$size]  = str_replace(MEDIA_DIR, "http://klimg.com/merdeka.com/cms/", $ret_dirresize[$size]);
            
            // cloud storage
            // if ( $this->CloudStorage != null)
            // {
            //     $object_source = $ret_dirresize[$size];
            //     $this->CloudStorage->upload_object(str_replace(config('url_dir.image_dir2'), '', $object_source), $object_source);
            // }
            // end cloud storage               
        }
        
        #harus di unremark ulang
        $sourcefile = array_merge($ret_dirresize, array( $target_img ));
        $this->send_to_server($sourcefile);

        $return = array(
            #'nama'      => $target_imgName,
            'nama' => $imgTarget_name . '.' . $ext,
            'type' => $ext,
            #'path'      => $target_dir,
            'path' => $imgTarget_dir . '/',
            'url_resize' => $ret_urlresize,
            'dir_resize' => $ret_dirresize,
            //'sourcefile'=> $sourcefile,
            'sourcefile' => '',
            'source_img' => $source_img,
            'target_img' => $target_img
        );


        return $return;
    }

    function cdnCopyAndResize ($imgReal_full_path, $imgTarget_name, $imgTarget_dir, $resize = array(), $metadata = array(), $addWatermark = false) {
        // echoPre($imgTarget_dir);
        # ---------------------------------------------------------------------------------------
        #   check, is $imgTarget_name is "real" path
        # ---------------------------------------------------------------------------------------
        
        $pattern = '/' . str_replace('/', '\/', config('url_dir.image_dir2')) . 'real\/*/';

        if ( preg_match($pattern, $imgTarget_dir) ) {
            $resize = Config::App()->get('real_image_resize');
        }
        # ---------------------------------------------------------------------------------------


        $ext = $this->getExtension($imgReal_full_path);
        
        if ( !in_array($ext, $this->_imageType) ) {

            return false;
        }

        $source_img = $imgReal_full_path;
        #$target_img = $imgTarget_dir . $imgTarget_name . $ext;
        $replace_size = str_pad(rand(0, pow(10, 10)-1), 10, '0', STR_PAD_LEFT);
        $imgTarget_name = preg_replace("/^((\d+|auto)x(\d+|auto))/", $replace_size, $imgTarget_name);
        $target_img = $imgTarget_dir . '/' . $imgTarget_name . '.' . $ext;

        //create dir
        $this->createDir($imgTarget_dir . '/');

        //echoPre($imgTarget_dir);

        if (!is_file($source_img))
            return false;
        //copy real image
        copy($source_img, $target_img) or die('Failed to copy :' . $source_img . '--to--' . $target_img);
        
        $this->optimizeImage($target_img);
        if ( is_array($metadata) && count($metadata) > 0 ) {
            $metadata['filename'] = $target_img;
            $ret_metadata = $this->metadata_image($metadata);
        }
        // cloud storage
        if ( $this->CloudStorage != null)
        {
            $object_source = $target_img;
            $this->CloudStorage->upload_object(str_replace(config('url_dir.image_dir2'), '', $object_source), $object_source);
        }
        // end cloud storage               
        
        //copy resized image
        $ret_dirresize = $ret_urlresize = array();
        if ( !is_array($resize) )
            $resize = array( $resize );

        foreach ( $resize as $key => $size ) {
            $crop = strpos($key, ":") ? $key : '';
            $ret_dirresize[$size] = $this->resize_image($target_img, $crop, $size, $addWatermark);
            //$ret_urlresize[$size]  = str_replace(MEDIA_DIR, "http://klimg.com/merdeka.com/cms/", $ret_dirresize[$size]);
            
            // cloud storage
            // if ( $this->CloudStorage != null)
            // {
            //     $object_source = $ret_dirresize[$size];
            //     $this->CloudStorage->upload_object(str_replace(config('url_dir.image_dir2'), '', $object_source), $object_source);
            // }
            // end cloud storage            
        }

        #harus di unremark ulang
        $sourcefile = array_merge($ret_dirresize, array( $target_img ));
        $this->send_to_server($sourcefile);

        // unlink($target_img);

        $return = array(
            #'nama'      => $target_imgName,
            'nama' => $imgTarget_name . '.' . $ext,
            'type' => $ext,
            #'path'      => $target_dir,
            'path' => $imgTarget_dir . '/',
            'url_resize' => $ret_urlresize,
            'dir_resize' => $ret_dirresize,
            //'sourcefile'=> $sourcefile,
            'sourcefile' => '',
            'source_img' => $source_img,
            'target_img' => $target_img
        );


        return $return;
    }

    function cdnResize ($imgReal_full_path, $imgTarget_name, $imgTarget_dir, $resize = array()) {
        # ---------------------------------------------------------------------------------------
        #   check, is $imgTarget_name is "real" path
        # ---------------------------------------------------------------------------------------
        
        $pattern = '/' . str_replace('/', '\/', config('url_dir.image_dir2')) . 'real\/*/';

        if ( preg_match($pattern, $imgTarget_dir) ) {
            $resize = Config::App()->get('real_image_resize');
        }
        # ---------------------------------------------------------------------------------------


        $ext = $this->getExtension($imgReal_full_path);

        if ( !in_array($ext, $this->_imageType) ) {
            return false;
        }

        $source_img = $imgReal_full_path;
        #$target_img = $imgTarget_dir . $imgTarget_name . $ext;
        $replace_size = str_pad(rand(0, pow(10, 10)-1), 10, '0', STR_PAD_LEFT);
        $imgTarget_name = preg_replace("/^((\d+|auto)x(\d+|auto))/", $replace_size, $imgTarget_name);
        $target_img = $imgTarget_dir . '/' . $imgTarget_name . '.' . $ext;

        //create dir
        $this->createDir($imgTarget_dir . '/');

        //copy resized image
        $ret_dirresize = $ret_urlresize = array();
        if ( !is_array($resize) )
            $resize = array( $resize );

        foreach ( $resize as $key => $size ) {
            $crop = strpos($key, ":") ? $key : '';
            $ret_dirresize[$size] = $this->resize_image($target_img, $crop, $size, false,[],$imgReal_full_path);              
        }

        #harus di unremark ulang
        $sourcefile = array_merge($ret_dirresize, array( $target_img ));
        $this->send_to_server($sourcefile);

        $return = array(
            #'nama'      => $target_imgName,
            'nama' => $imgTarget_name . '.' . $ext,
            'type' => $ext,
            #'path'      => $target_dir,
            'path' => $imgTarget_dir . '/',
            'url_resize' => $ret_urlresize,
            'dir_resize' => $ret_dirresize,
            //'sourcefile'=> $sourcefile,
            'sourcefile' => '',
            'source_img' => $source_img,
            'target_img' => $target_img
        );


        return $return;
    }

    function rezise_image ($imgReal_full_path, $imgTarget_name, $imgTarget_dir, $resize = array()) {
        # ---------------------------------------------------------------------------------------
        #   check, is $imgTarget_name is "real" path
        # ---------------------------------------------------------------------------------------
        
        $pattern = '/' . str_replace('/', '\/', config('url_dir.image_dir2')) . 'real\/*/';

        if ( preg_match($pattern, $imgTarget_dir) ) {
            $resize = Config::App()->get('real_image_resize');
        }
        # ---------------------------------------------------------------------------------------


        $ext = $this->getExtension($imgReal_full_path);

        if ( !in_array($ext, $this->_imageType) ) {
            return false;
        }

        $source_img = $imgReal_full_path;
        #$target_img = $imgTarget_dir . $imgTarget_name . $ext;
        $replace_size = str_pad(rand(0, pow(10, 10)-1), 10, '0', STR_PAD_LEFT);
        $imgTarget_name = preg_replace("/^((\d+|auto)x(\d+|auto))/", $replace_size, $imgTarget_name);
        $target_img = $imgTarget_dir . '/' . $imgTarget_name . '.' . $ext;

        //create dir
        $this->createDir($imgTarget_dir . '/');

        //copy real image
        //copy($source_img, $target_img) or die('Failed to copy :' . $source_img . '--to--' . $target_img);
        //copy resized image
        $ret_dirresize = $ret_urlresize = array();
        if ( !is_array($resize) )
            $resize = array( $resize );

        foreach ( $resize as $key => $size ) {
            $crop = strpos($key, ":") ? $key : '';
            $ret_dirresize[$size] = $this->resize_image($target_img, $crop, $size, false);
            //$ret_urlresize[$size]  = str_replace(MEDIA_DIR, "http://klimg.com/merdeka.com/cms/", $ret_dirresize[$size]);
            
            // cloud storage
            // if ( $this->CloudStorage != null)
            // {
            //     $object_source = $ret_dirresize[$size];
            //     $this->CloudStorage->upload_object(str_replace(config('url_dir.image_dir2'), '', $object_source), $object_source);
            // }
            // end cloud storage                
        }

        #harus di unremark ulang
        $sourcefile = array_merge($ret_dirresize, array( $target_img ));
        $this->send_to_server($sourcefile);

        $return = array(
            #'nama'      => $target_imgName,
            'nama' => $imgTarget_name . '.' . $ext,
            'type' => $ext,
            #'path'      => $target_dir,
            'path' => $imgTarget_dir . '/',
            'url_resize' => $ret_urlresize,
            'dir_resize' => $ret_dirresize,
            //'sourcefile'=> $sourcefile,
            'sourcefile' => '',
            'source_img' => $source_img,
            'target_img' => $target_img
        );


        return $return;
    }

    function rezizeit ($file, $resize, $addWatermark = false) {
        
        
        $ret_resize = false;
        if ( is_array($resize) && count($resize) ) {
            $ret_dirresize = array();
            foreach ( $resize as $key => $size ) {
                $crop = strpos($key, ":") ? $key : '';
                $ret_dirresize[$size] = $this->resize_image($file, $crop, $size, $addWatermark);
                
                // cloud storage
                // if ( $this->CloudStorage != null)
                // {
                //     $object_source = $ret_dirresize[$size];
                //     $this->CloudStorage->upload_object(str_replace(config('url_dir.image_dir2'), '', $object_source), $object_source);
                // }
                // end cloud storage                   
            }
        }
        return $ret_dirresize;
    }

    public function optimizeImage($source, $asset = false){
        if ($asset === false) {
            
            //optimize JPEG
            if (preg_match('/\.jpg/i', $source)) {
                exec('/usr/bin/jpegoptim --strip-all ' . $source);
            }

            //optimize PNG
            if (preg_match('/\.png/i', $source)) {
                exec('/usr/bin/optipng -o7 ' . $source);
            }
        } 
    }

    /**
     * function to update image metadata
     */
    function metadata_image ($metadata_conf) {
        return false; // Adi 2016/12/19; not support on php7
        if ( is_array($metadata_conf) && count($metadata_conf) > 0 ) {
            include_once 'metadata/Toolkit_Version.php';
            error_reporting(0);
            include_once 'metadata/JPEG.php';
            include_once 'metadata/XMP.php';
            include_once 'metadata/Photoshop_IRB.php';
            include_once 'metadata/EXIF.php';
            include_once 'metadata/Photoshop_File_Info.php';


            // Copy all of the HTML Posted variables into an array
            //$new_ps_file_info_array = $GLOBALS['HTTP_POST_VARS'];
            $new_ps_file_info_array = $metadata_conf;

            $filename = $new_ps_file_info_array['filename'];
            //echo $filename;
            // Protect against hackers editing other files

            $path_parts = pathinfo($filename);
            $array_extention = array( 'png', 'jpg' );
            if ( strcasecmp($path_parts["extension"], "jpg") != 0 ) {
                //if (!in_array($path_parts["extension"], $array_extention))
                #echo "Incorrect File Type - JPEG Only\n";
                return array( 'status' => 'failed', 'message' => 'Incorrect File Type - JPEG Only' );
                exit();
            }
            // Change: removed limitation on file being in current directory - as of version 1.11
            // Retrieve the header information
            $jpeg_header_data = get_jpeg_header_data($filename);

            // Retreive the EXIF, XMP and Photoshop IRB information from
            // the existing file, so that it can be updated
            $Exif_array = get_EXIF_JPEG($filename);
            $XMP_array = read_XMP_array_from_text(get_XMP_text($jpeg_header_data));
            $IRB_array = get_Photoshop_IRB($jpeg_header_data);

            // Update the JPEG header information with the new Photoshop File Info
            $jpeg_header_data = put_photoshop_file_info($jpeg_header_data, $new_ps_file_info_array, $Exif_array, $XMP_array, $IRB_array);

            // Check if the Update worked
            if ( $jpeg_header_data == FALSE ) {
                //echo '$jpeg_header_data false';
                return array( 'status' => 'failed', 'message' => 'Error - Failure update Photoshop File Info : ' . $filename );
                // Abort processing
                exit();
            }

            // Attempt to write the new JPEG file
            if ( FALSE == put_jpeg_header_data($filename, $filename, $jpeg_header_data) ) {
                //echo 'put_jpeg_header_data false';
                return array( 'status' => 'failed', 'message' => 'Error - Failure to write new JPEG : ' . $filename );
                // Abort processing
                exit();
            }
            return array( 'status' => 'succes', 'message' => $filename . ' updated' );
        }
    }

    /**
     * build cropping canvas with various size
     * @param   cropConf    array(
     *                          imgSrc      => 'string' image source file
     *                              ex : /data/shopkl/_assets/img/banner/34/filename.jpg
     *                          uniqueID    => 'int' primary key from specific sontent like 'banner_id | article_id | news_id' etc
     *                              ex : 4345
     *                          cropSize    => 'array'  set of aray config consist ratio, size and path
     *                              ex : cropSize = array(
     *                                                  'size'  => array('2:3' => '200x300', '4:4' => '400x400'),
     *                                                  'path'  => array('2:3' => '/data/shopkl/_assets/img/', '4:4' => '/data/shopkl/_assets/img/')
     *                                              )
     *                                  )
     *                          imgUrl      => 'string' image url to load in <img> html tag
     *                              ex : http://klimg.com/kapanlagi.com/banner/34/filename.jpg
     *                          submitUrl   => 'string' form action url
     *                              ex : http://admin.kapanlagi.com/banner/update/34/
     *                          include_freeResize => 'boolean' if true the in combo size option will be added Free resize
     *                              default = true
     *                          ajaxSubmit  => 'boolean' if true them form submit will be send as ajax
     *                              default = false
     *                      )
     * ps : this feature need external plugin :
     * jquery.form
     * jquery.colorbox
     * jquery.jCrop
     */
    function build_cropCanvas ($cropConf = array()) {
        $conf['imgSrc'] = (isset($cropConf['imgSrc'])) ? $cropConf['imgSrc'] : false;
        $conf['uniqueID'] = (isset($cropConf['uniqueID'])) ? $cropConf['uniqueID'] : false;
        $conf['cropSize'] = (isset($cropConf['cropSize'])) ? $cropConf['cropSize'] : false;
        $conf['imgUrl'] = (isset($cropConf['imgUrl'])) ? $cropConf['imgUrl'] : false;
        $conf['submitUrl'] = (isset($cropConf['submitUrl'])) ? $cropConf['submitUrl'] : false;
        $conf['include_freeResize'] = (isset($cropConf['include_freeResize'])) ? $cropConf['include_freeResize'] : false;
        $conf['ajaxSubmit'] = (isset($cropConf['ajaxSubmit'])) ? $cropConf['ajaxSubmit'] : false;

        foreach ( $conf as $key => $c )
            if ( $c == false && $key != 'ajaxSubmit' )
                return 'Insufficient Parameter options';

        #echo $conf['imgSrc'];
        $size = getimagesize($conf['imgSrc']);
        $img_name = explode("/", $conf['imgSrc']);
        $img_name = end($img_name);

        //img size option
        $option = $img_dest = $savePath = '';
        foreach ( $conf['cropSize']['size'] as $key => $i ) {
            #$option .= '<option value="'.$i.'">'.$i.'</option>';
            $img_dest .= 'img_dest["' . $i . '"] = "' . $conf['cropSize']['path'][$key] . '"; ';
            if ( !$savePath )
                $savePath = $conf['cropSize']['path'][$key] . (($conf['uniqueID']) ? $conf['uniqueID'] . '/' : '');
        }
        #$option .= '<option value="200x100">200x100</option>';
        if ( $conf['include_freeResize'] )
            $option .= '<option value="free">Free Size</option>';
        //end
        #echo '<pre>'; print_r($img_dest); echo '</pre>';

        $content = '
            <div style="min-width:700px; padding:10px 0 10px 30px;">
            <form id="frCrop" action="' . $conf['submitUrl'] . '" method="POST">
                <div class="row-fluid">
                    <div class="span4">
                        <h3>Cropping Setting</h3>
                        <label>Cropped Image size : </label>
                        <select name="cSize" id="cSize">' . $option . '</select>
                        <input type="hidden" name="img_newName" value="' . $img_name . '" />
                        <input type="hidden" name="uniqueID" value="' . $conf['uniqueID'] . '" />

                        <input type="hidden" id="x" name="x" />
                        <input type="hidden" id="y" name="y" />
                        <input type="hidden" id="w" name="w" />
                        <input type="hidden" id="h" name="h" />
                        <input type="hidden" id="img_real" name="img_real" value="' . $conf['imgUrl'] . '" />
                        <label>Cropped Image destination : </label>
                        <input type="hidden" name="dest_path" id="dest_path" value="' . $savePath . '" />

                        <div class="alert">select image size then drag mouse inside image area to crop !</div>
                        <input type="button" value="Crop" name"crop" id"Savecrop" class="btn btn-primary" onclick="act_cropSave()" />
                        <span id="sp-loadCrop" class="hide"><img src="'.Config::App()->get('assets_image_url').'loading.gif" alt="loading" /> loading..</span>
                    </div>
                    <div style="float:left; width:600px; margin-left:20px;">
                        <img src="' . $conf['imgUrl'] . '" id="cropbox" width="600" />
                    </div>
                </div>
            </form>
            <br class="clear" />
            </div>
            <script>
                function initCrop(){
                    var img_dest = new Array();
                    ' . $img_dest . '
                    var snil = $("#cSize").val();
                    nil         = snil.split("x");

                    var jcrop_api = $.Jcrop(\'#cropbox\', {
                        trueSize: [' . $size[0] . ',' . $size[1] . '],
                        aspectRatio: nil[0] / nil[1],
                        onSelect: updateCoords
                    });

                    $("#cSize").bind("change", function(){
                        var snil    = $(this).val();
                        nil         = snil.split("x");
                        alert(nil);
                        jcrop_api.setOptions({
                            aspectRatio: nil[0] / nil[1]
                        });
                        $("#dest_path").val( img_dest[snil] );
                    });

                    ' . (($conf['ajaxSubmit']) ? ('
                        $("#frCrop").ajaxForm({
                            dataType:   "json",
                            beforeSubmit: function(){
                                $("#sp-loadCrop").show();
                            },
                            success:    function(data){
                                //alert(data);
                                $(".alert").replaceWith(data.msg);
                                $("#sp-loadCrop").hide();
                            }
                        });
                    ') : '') . '

                    function updateCoords(c)
                    {
                        $("#x").val(c.x);
                        $("#y").val(c.y);
                        $("#w").val(c.w);
                        $("#h").val(c.h);
                    };

                    function checkCoords()
                    {
                        if (parseInt($("#w").val())) return true;
                        alert("Please select a crop region then press submit.");
                        return false;
                    };
                }
            </script>
        ';

        return $content;
    }

    /**
     * get file extension based on file name
     * @param   name    string  filename
     * @return  string
     */
    function getExtension ($name) { 
        if ( is_file($name) || strpos($name, "http://") !== false || strpos($name, "https://") !== false ) {
            
            // $info = new SplFileInfo($name);
            // $extension = $info->getExtension();
            $extension = \File::extension($name);
            if($extension=='')
            {
                $info = getimagesize($name);
                $extension = str_replace("image/", "", $info['mime']);
            }

            switch ( $extension ) {
                case 'jpg' :
                    return 'jpg';
                case 'jpeg' :
                    if(strpos($name, $extension) !== false){
                        return 'jpeg';
                    }else{
                        return 'jpg';
                    }
                    break;
                case 'png' :
                    return 'png';
                    break;
                case 'gif' :
                    return 'gif';
                    break;
                default:
                    return $extension;
                    break;
            }
        }
        else {
            $explode_file = explode('.', $name);
            return strtolower(end($explode_file));
        }
    }

    /**
     * create image based on image resource
     */
    private function createimage ($imgSrc, $extension) {
        //echo "$imgSrc,$extension";
        ini_set('gd.jpeg_ignore_warning', 1);
        $info = getimagesize($imgSrc);
        $extension = str_replace("image/", "", $info['mime']);
        switch ( $extension ) {
            case 'jpg' :
                $image = imagecreatefromjpeg($imgSrc);
                break;
            case 'jpeg' :
                $image = imagecreatefromjpeg($imgSrc);
                break;
            case 'png' :
                $image = imagecreatefrompng($imgSrc);
                break;
            case 'gif' :
                $image = imagecreatefromgif($imgSrc);
                break;
        }
        //echo $image;die();
        return $image;
    }

    function get_image_size($image){
        if(@is_array(getimagesize($image))){
            $image = ImageCreateFromString(file_get_contents($image));
            return array('width' => imagesx($image), 'height' => imagesy($image));
        } else {
            return array('width' => 0, 'height' => 0);
        }
        
    }

    function resize_image ($image, $crop = false, $size = null, $addWatermark = false, $metadata = array(), $img_source_full = "") {
        $old_image = $image;
        
        // $info = new SplFileInfo($image);
        $extension = \File::extension($image);
        
  //    if($info->getExtension()=='png' || $info->getExtension()=='gif')
        // {
        //  return false;
        // }

        $file = $image;
        if(!empty($img_source_full)){
            $old_image = $img_source_full;
            $image = ImageCreateFromString(file_get_contents($img_source_full));
        }else{
            $image = ImageCreateFromString(file_get_contents($image));
        }

        if ( is_resource($image) === true ) {
            $x = 0;
            $y = 0;
            $width = imagesx($image);
            $height = imagesy($image);

            /*
              CROP (Aspect Ratio) Section-----------------------------------------------------------------
             */

            if ( $crop == false ) {
                $crop = array( $width, $height );
            }
            else {

                $crop = array_filter(explode(':', $crop));
                // $crop = explode(':', $crop); print_r($crop);
                //detect if width and height ratio is "auto"
                if ( $crop[1] == 'auto' && $crop[0] === 'auto' )
                    $crop = array( $width, $height );

                //detect if height ratio is "auto"
                if ( $crop[1] == 'auto' )
                    $crop[1] = intval(($crop[0] * $height) / $width);
                //end
                //detect if width ratio is "auto"
                if ( $crop[0] == 'auto' )
                    $crop[0] = intval(($crop[1] * $width) / $height);
                //end

                if ( empty($crop) === true ) {
                    $crop = array( $width, $height );
                }
                else {
                    if ( (empty($crop[0]) == true) || (is_numeric($crop[0]) == false) ) {
                        $crop[0] = $crop[1];
                    }
                    else if ( (empty($crop[1]) == true) || (is_numeric($crop[1]) == false) ) {
                        $crop[1] = $crop[0];
                    }
                }

                $ratio = array( 0 => (int)$width / (int)$height, 1 => (int)$crop[0] / (int)$crop[1] );

                if ( $ratio[0] > $ratio[1] ) {
                    $width = $height * $ratio[1];
                    $x = (imagesx($image) - $width) / 2;
                }
                else if ( $ratio[0] < $ratio[1] ) {
                    $height = $width / $ratio[1];
                    $y = (imagesy($image) - $height) / 2;
                }
            }

            /*
              Resize Section-------------------------------------------------------------------------------
             */

            if ( is_null($size) === true ) {
                $size = array( $width, $height );
                $indexedSize = $width . "x" . $height;
            }
            else {
                $indexedSize = $size;
                $size = array_filter(explode('x', $size));
                if ( empty($size) === true ) {
                    $size = array( imagesx($image), imagesy($image) );
                }
                else {
                    if ( (empty($size[0]) === true) || (is_numeric($size[0]) === false) ) {
                        $size[0] = round($size[1] * $width / $height);
                    }
                    else if ( (empty($size[1]) === true) || (is_numeric($size[1]) === false) ) {
                        $size[1] = round($size[0] * $height / $width);
                    }
                }
            }

            $result = ImageCreateTrueColor($size[0], $size[1]);

            //get file ext and remove it
            $filename = explode('.', $file);
            $ext = "." . end($filename);
            array_pop($filename);
            $filename = implode('.', $filename);

            //insert image resized dimension into image name
            $filename = explode("/", $filename);
            #$img_name = implode("x", $size) . '-' . end($filename);

            $img_name = $indexedSize . '-' . end($filename);
            array_pop($filename);
            $filepath = implode('/', $filename);

            $filename = $filepath . '/' . $img_name . $ext;

            if ( is_file($filename) )
                unlink($filename);
            #imagejpeg($result, $filename, 100);

            if ( is_resource($result) === true ) {
                ImageSaveAlpha($result, true);
                ImageAlphaBlending($result, true);
                ImageFill($result, 0, 0, ImageColorAllocate($result, 255, 255, 255));
                //ImageCopyResampled($result, $image, 0, -10, $x, $y, $size[0], $size[1], $width, $height); // upload axis y start at -20
                ImageCopyResampled($result, $image, 0, 0, $x, $y, $size[0], $size[1], $width, $height); // upload axis y start at -20
                ImageInterlace($result, true);
                // vd($x.'  -  '.$y);
                if($extension=='gif')
                {
                    system("convert ".$old_image." -coalesce -repage ".$width."x".$height." -resize ".$size[1]."x".$size[0]." -layers Optimize ".$filename);
                } else 
                {
                    ImageJPEG($result, $filename, 80);
                }
                
                //give watermark only to the image with width more than 500px
                //if ($size[0] >= 500 && $addWatermark)
                //{
                //    $this->create_watermark($filename, $filename);
                //}
                //give watermark only to the image with width more than 500px
                if ( $size[0] >= 435 && $addWatermark ) {
                    $this->create_watermark($filename, $filename, $addWatermark);
                }

                if(!file_exists($filename))
                    chmod($filename, 0644);
                
                $this->optimizeImage($filename);
                if ( is_array($metadata) && count($metadata) > 0 ) {
                    $metadata['filename'] = $filename;
                    $ret_metadata = $this->metadata_image($metadata);
                }
                imagedestroy($result);
            }
            else {
                $filename = false;
            }
        }

        return $filename;
    }

    function set_watermark ($imageFile) {

    }

    /**
     * function to put watermark on image above 500 in width
     * @param   string  $source_file_path   full path + file name of the resized image (ex: /data/klshop/_assets/img/client/xxx/product/100/xxx.jpg)
     * @param   string  $output_file_path   full path name of the saved image with watermarked img
     * usage : create_watermark($source_file_path, $output_file_path)
     */
    function create_watermark ($source_file_path, $output_file_path, $addWatermark = false) {
        //$this->set_watermark($source_file_path);
        #echo $source_file_path.' - '.$output_file_path.'<br>';

        $addWatermark = false;
        if ( $addWatermark ) {
            //echo $imgtemp = str_replace('.jpg','.png',$output_file_path);

            $watermark = imagecreatefrompng($this->watermark_file);

            $watermark_width = imagesx($watermark);
            $watermark_height = imagesy($watermark);

            //$photo = imagecreatetruecolor($watermark_width, $watermark_height);
            $photo = imagecreatefromjpeg($source_file_path);

            /* untuk mengatasi image dengan type png-24 */
            imagealphablending($photo, true);
            imagealphablending($watermark, true);

            $photo_x = imagesx($photo);
            $watermark_x = imagesx($watermark);
            $photo_y = imagesy($photo);
            $watermark_y = imagesy($watermark);

            imagecopy($photo, $watermark, (($photo_x) - ($watermark_x)) - 5, (($photo_y) - ($watermark_y)) - 5, 0, 0, $watermark_x, $watermark_y);

            imagejpeg($photo, $output_file_path, 100);

            chmod($output_file_path, 0644);
        }
    }

    /**
     * function to crop image based on specific area and size
     * @param   param   array   set of array contain parameter needed in cropping action
     *                      ex : array(
     *                              'final_size'    => string width x height size of cropped image result
     *                              'img_newname'   => string image name for cropped image result
     *                              'dest_path'     => string path where cropped image is going to be stored
     *                              'img_real'      => string image location of the source image
     *                              'x'             => number image x crop position
     *                              'y'             => number image y crop position
     *                              'w'             => number image width crop size
     *                              'h'             => number image height crop size
     *                           )
     */
    function cropImage ($param = array()) {
        
        
        // $info = new SplFileInfo($param['img_real']);
        $info = \File::extension($param['img_real']);

        $ret = array(
            'msg' => false,
            'sts' => false,
        );

        $final_size = $param['final_size'];
        #$filename       = $final_size.'-'.$param['img_newname'];
        $filename = $param['img_newname'];
        $target_dir = $param['dest_path'];
        $target_name = $target_dir . $filename;
        $source_name = $param['img_real'];

        //get size from real image
        $size = getimagesize($source_name);
        $targ_w = $param['w'];
        $targ_h = $param['h'];

        //get size from ratio
        $final_size = explode("x", $final_size);
        $final_w = $final_size[0];
        $final_h = $final_size[1];

        if ( $final_w === 'auto' && $final_h === 'auto' ) {   //detect if width and height ratio is "auto" then readjust width and height size
            $final_w = $targ_w;
            $final_h = $targ_h;
        }
        elseif ( $final_w === 'auto' ) {   //detect if width ratio is "auto" then readjust width size
            $final_w = intval(($final_size[1] * $targ_w) / $targ_h);
        }
        elseif ( $final_h === 'auto' ) {   //detect if height ratio is "auto" then readjust height size
            $final_h = intval(($final_size[0] * $targ_h) / $targ_w);
        }
        //end


        $jpeg_quality = 90;

        switch ( exif_imagetype($source_name) ) {
            case 1:
                //gif
                $img_r = imagecreatefromgif($source_name);
                break;
            case 2:
                //jpg
                $img_r = imagecreatefromjpeg($source_name);
                break;
            case 3:
                //png
                $img_r = imagecreatefrompng($source_name);
                break;
        }

        $dst_r = ImageCreateTrueColor($final_w, $final_h);
        /*$color = imagecolorallocate($dst_r, 255,255,255);
        imagefill($dst_r, 0, 0,$color);
        imagecopy($dst_r, $img_r, $param['x'], $param['y'], 0, 0, $final_w, $final_h);*/

        imagecopyresized(
                $dst_r, $img_r, 0, 0, $param['x'], $param['y'], $final_w, $final_h, $param['w'], $param['h']
        );

        if($info=='gif')
        {
            system("convert ".$source_name." -coalesce -repage ".$param['w']."x".$param['h']." -resize ".$final_h."x".$final_w." -layers Optimize ".$target_name);
        } else 
        {
            imagejpeg($dst_r, $target_name, $jpeg_quality);
        }
        
        $this->send_to_server(array( $target_name ));
        $url_target_name = $target_name;
        // cloud storage
        if ( $this->CloudStorage != null)
        {
            $object_source = $target_name;
            $this->CloudStorage->upload_object(str_replace(config('url_dir.image_dir2'), '', $object_source), $object_source);
        }
        // end cloud storage          


        //find image parent url and dir path from config
        //reset($this->imgsize['article']['size']);
        //$first_key = key($this->imgsize['article']['size']);
        //end find
        $ret['sts'] = true;
        $ret['filename'] = $filename;
        $ret['msg'] = '<div class="alert alert-success">Image is cropped and saved in <a href="' . $url_target_name . '?' . date('his') . '" target="_blank">' . $target_name . ' !</a></div>';

        return $ret;
    }

    /**
     * recursively create directory
     * @param   path    string  string directory path
     */
    function createDir ($path) {
        $arr = explode("/", $path);
        $dir = "";
        foreach ( $arr as $r ) {
            if ( $r ) {
                $dir = $dir . "/" . $r;
                if ( !file_exists($dir) ) {
                    mkdir($dir, 0777);
                }
            }
        }
    }

    /**
     * WARNING : Be careful with this function
     */
    function deleteDir ($dirPath) {
        if ( is_dir($dirPath) ) {
            if ( substr($dirPath, strlen($dirPath) - 1, 1) != '/' ) {
                $dirPath .= '/';
            }
            $files = scandir($dirPath);
            foreach ( $files as $file ) {
                if (in_array($file, ['.','..']))
                    continue;
                if ( is_dir($dirPath.$file) ) {
                    self::deleteDir($dirPath.$file.'/');
                }
                else {
                    unlink($dirPath.$file);
                    #echo "unlink($file)<br />";
                }
            }
            rmdir($dirPath);
            //echo "rmdir($dirPath)<br />";
        }
        else {
            #throw new InvalidArgumentException('$dirPath must be a directory');
        }
    }

    /**
     * function to copy dir and its content to a new location
     */
    function recursive_copy ($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while ( false !== ( $file = readdir($dir)) ) {
            if ( ( $file != '.' ) && ( $file != '..' ) ) {
                if ( is_dir($src . '/' . $file) ) {
                    recursive_copy($src . '/' . $file, $dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * function to rotate image JPG, PNG, GIF
     * @param array variabel
     *        array(
     *          src => image source, real path
     *          newname => image save as new name
     *          deg => deegre of rotation
     *        )
     */
    function rotateImage ($param = array()) {
        
        
        $img = isset($param['src']) ? $param['src'] : false;
        $newname = isset($param['newname']) ? $param['newname'] : $img;
        $degrees = isset($param['deg']) ? $param['deg'] : false;
        $out = false;

        if ( $img ) {

            switch ( exif_imagetype($img) ) {
                case 1:
                    //gif
                    $destimg = imagecreatefromgif($img);
                    $transColor = imagecolorallocatealpha($destimg, 255, 255, 255, 127);
                    $rotatedImage = imagerotate($destimg, $degrees, $transColor);
                    imagesavealpha($rotatedImage, true);
                    imagegif($rotatedImage, $newname);
                    break;
                case 2:
                    //jpg
                    $destimg = imagecreatefromjpeg($img);
                    $transColor = imagecolorallocatealpha($destimg, 255, 255, 255, 127);
                    $rotatedImage = imagerotate($destimg, $degrees, $transColor);
                    imagesavealpha($rotatedImage, true);
                    imagejpeg($rotatedImage, $newname);
                    break;
                case 3:
                    //png
                    $destimg = imagecreatefrompng($img);
                    $transColor = imagecolorallocatealpha($destimg, 255, 255, 255, 127);
                    $rotatedImage = imagerotate($destimg, $degrees, $transColor);
                    imagesavealpha($rotatedImage, true);
                    imagepng($rotatedImage, $newname);
                    break;
            }

            $out = $img;
            
            // cloud storage
            if ( $this->CloudStorage != null)
            {
                $object_source = $newname;
                $this->CloudStorage->upload_object(str_replace(config('url_dir.image_dir2'), '', $object_source), $object_source);
            }
            // end cloud storage              
        }

        return $out;
    }

//----------------------------------------------------------------------------------------------------------------



    function send_to_server ($filesource = array()) {
        // aws, disable send to server
        return true;

        //include("/data/kapanlagi/development/applications/admin_men/config/ftp.php");
        //include("/data/kapanlagi/development/applications/admin_men/config/url_dir.php");

        
        $return = false;
        if ( is_array($filesource) ) {
            $ftp_server = '';
            $ftp_started = false;
            foreach ( $filesource as $key => $f ) {
                if ( file_exists($f) && preg_match('/\/media\//', $f) ) {
                    //config('url_dir.image_dir2').'<br />';
                    $temp = explode('/', str_replace(config('url_dir.image_dir2'), '', $f));
                    $folder = $temp[0];

                    if ( !$ftp_started ) {
                        if ( $conn = ftp_connect($C->config['default']['ftp_host']) ) {
                            if ( ftp_login($conn, $C->config['default']['ftp_username'], $C->config['default']['ftp_password']) ) {
                                $ftp_started = true;
                            }
                            else {
                                ftp_close($conn);
                                $return = false;
                            }
                        }
                        else {
                            ftp_close($conn);
                            $return = false;
                        }
                    }

                    //echo $C->config['sitename'];
                    $target = $C->config['default']['ftp_rootfolder'];
                    #$target = '/kapanlagi.com';
                    #$source = MEDIA_DIR.$folder;
                    $source = config('url_dir.image_dir2');
                    #echo $source.'<br />';

                    for ( $i = 0; $i <= count($temp) - 1; $i++ ) {
                        $target .= '/' . $temp[$i];
                        $source .= '/' . $temp[$i];
                        //echo '$target : '.$target.'<br />';
                        //echo '$source : '.$source.'<br />';

                        if ( is_dir($source) ) {
                            //echo $source.'=='.$target.'<br />';
                            @ftp_mkdir($conn, $target);
                        }
                        elseif ( is_file($source) ) {

                            //optimize JPEG files - reyno (20120308)
                            if ( preg_match('/\.jpg/i', $source) ) {
                                exec('/usr/local/bin/jpegoptim --strip-all ' . $source);
                            }

                            //optimize PNG files - reyno (20120312)
                            if ( preg_match('/\.png/i', $source) ) {
                                exec('/usr/local/bin/optipng -o7 ' . $source);
                            }

                            if ( !@ftp_put($conn, $target, $source, FTP_BINARY) ) {
                                // file_put_contents($this->log_location, date('Y-m-d H:i:s').' | '. $source."\n", FILE_APPEND);
                                $return = false;
                                //var_dump("failed");
                            }
                            else {
                                unset($this->filesource[$key]);
                                $success[] = str_replace('/sooperboy.com', 'http://cdn.klimg.com/sooperboy.com', $target) . "?" . date("Ymdhis");
                                //var_dump("success");
                            }
                        }
                    }
                }
            }

            if ( $ftp_started ) {
                ftp_close($conn);
            }
            $return = true;
        }
        
        return $return;
    }

    function delete_from_server ($filesource = array()) {
        $return = false;
        if ( is_array($filesource) ) {
            $ftp_server = '';
            $ftp_started = false;
            foreach ( $filesource as $f ) {
                $temp = explode('/', str_replace(Config::get('klimg_dir'), '', $f));
                $folder = $temp[0];

                if ( !$ftp_started ) {
                    if ( $conn = ftp_connect(Config::get('ftp_host')) ) {
                        if ( ftp_login($conn, Config::get('ftp_username'), Config::get('ftp_password')) ) {
                            $ftp_started = true;
                        }
                        else {
                            $return = false;
                        }
                    }
                    else {
                        ftp_close($conn);
                        $return = false;
                    }
                }

                $target = Config::get('ftp_rootfolder') . str_replace(Config::get('klimg_dir') . $folder, '', $f);
                if ( is_dir($f) ) {
                    if ( @ftp_rmdir($conn, $target) ) {
                        //@rmdir($f);
                    }
                }
                else {
                    if ( @ftp_delete($conn, $target) ) {
                        //@unlink($f);
                    }
                }
            }

            if ( $ftp_started ) {
                ftp_close($conn);
            }
            $return = true;
        }
        return $return;
    }

}

/* eof */