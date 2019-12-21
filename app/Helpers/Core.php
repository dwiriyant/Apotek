<?php

use App\Toko;

function clean_post ($content = "", $clean = true) {
    if ( $content != "" ) {
        $var = $content;
        if ( is_array($var) ) {
            foreach ( $var as $v ) {
                $var1[] = sanitizeString($v, $clean);
            }

            $var = $var1;
        }
        else {
            $var = sanitizeString($var, $clean);
        }
    }

    return $var;
}

function sanitizeString ($var, $clean = true) {

    $var = stripslashes($var);
    if ( $clean )
        $var = htmlentities($var);
    $var = strip_tags($var);
    /*
     * Remarked by oedien
     * it's remarked because this feature is also availble in db library (database.php),
     * same to mysql_real_escape();
     */
    #$var = addcslashes($var, "\x00\n\r\'\"\x1a");

    return $var;
}

function createDir ($path) {
    File::makeDirectory($path,0777,true);
}

function offset($page, $limit){
    return ($page - 1) * $limit;
}

function vd($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    exit();
}

function get($index = false, $default = false){
    if ($index === false)
        return $_GET;
    return (isset($_GET[$index])) ? $_GET[$index] : $default;
}

function post($index = false, $default = false){
    if ($index === false)
        return $_POST;
    return (isset($_POST[$index])) ? $_POST[$index] : $default;
}

if (!function_exists('isAjax')){

    function isAjax(){
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

}

function getRealDimension($txtsize, $maxwidth, $width = false){
    if ($txtsize == '0x0')
        return $txtsize;
    $tmp = explode("x", $txtsize);
    if ($width == false){
        $ratio = round($maxwidth / $tmp[0], 2);
        $height = round($ratio * $tmp[1]);
        return $maxwidth.'x'.$height;
    }else{
        $ratio = round($maxwidth / $tmp[1], 2);
        $height = round($ratio * $tmp[0]);
        return $height.'x'.$maxwidth;
    }
}

function getFormattedDate($src, $dest_format = 'd-m-Y', $src_format = 'Y-m-d H:i:s')
{
    $date = DateTime::createFromFormat($src_format, $src);
    if ($date)
        return $date->format($dest_format);
    else
        return '';
}

function isPost () {
    return ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false;
}

function htmlEncode($str){
    return htmlentities($str);
}

if ( !function_exists('set_flash') ) {
    function set_flash($message, $index = 'flash')
    {
        $_SESSION['flash_message'][$index] = $message;
    }
}

if ( !function_exists('has_flash') ) {
    function has_flash($index = 'flash')
    {
        return isset($_SESSION['flash_message'][$index]);
    }
}

if ( !function_exists('get_flash') ) {
    function get_flash($index = 'flash')
    {
        $tmp = (isset($_SESSION['flash_message'][$index])) ? $_SESSION['flash_message'][$index] : '' ;
        unset($_SESSION['flash_message'][$index]);
        return $tmp;
    }
}

function removeQsKey($url, $key)
{
    $url = preg_replace('/(?:&|(\?))' . $key . '=[^&]*(?(1)&|)?/i', "$1", $url);

    return $url;
}

function referer($route, $param = false){
    return (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : url($route, $param) ;
}

if ( !function_exists('url_title') ) {

    /**
     * url_title()
     *
     * @param mixed $str
     * @param string $separator
     * @param bool $lowercase
     * @return
     */
    function url_title ($str, $separator = 'dash', $lowercase = true, $lenght = 200) {
        if ( $separator == 'dash' ) {
            $search = '_';
            $replace = '-';
        }
        else {
            $search = '-';
            $replace = '_';
        }

        $trans = array(
            '&\#\d+?;' => '',
            '&\S+?;' => '',
            '\s+' => $replace,
            '[^a-z0-9\-\._]' => '',
            $replace . '+' => $replace,
            $replace . '$' => $replace,
            '^' . $replace => $replace,
            '\.+$' => ''
        );
        if(is_array($str))
            $str = json_encode($str);
        $str = strip_tags($str);
        if ( strlen($str) > $lenght ) {
            $str = substr($str, 0, $lenght);
        }

        foreach ( $trans as $key => $val ) {
            $str = preg_replace("#" . $key . "#i", $val, $str);
        }

        if ( $lowercase === true ) {
            $str = strtolower($str);
        }

        return trim(stripslashes(str_replace(array( ',', '.' ), array( '', '' ), $str)));
    }

}

function transformDate($value, $format = 'Y-m-d')
{
    try {
        return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
    } catch (\ErrorException $e) {
        return \Carbon\Carbon::createFromFormat($format, $value);
    }
}

function getToko($key = 'nama')
{
    $toko = Toko::first();
    return strtolower($toko->$key);
}

/* eof */