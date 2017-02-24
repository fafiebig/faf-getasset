<?php

define('WP_USE_THEMES', false);
require(dirname(__FILE__).'/../../../wp//wp-blog-header.php');

// extract hash
$hashed = base64_decode($_REQUEST['asset']);
$params = explode('|', $hashed);

// bail out early
if (!is_numeric($params[0]) || !isset($params[1])) {
    header('HTTP/1.0 404 Not found');
    exit;
}

// what is what
$id     = $params[0];
$size   = $params[1];

// check private
$private    = get_post_meta($id, 'private', true);
if ($private === 'yes' && !is_user_logged_in()) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// return file content
if ( $file = get_post_meta( $id, '_wp_attached_file', true ) ) {

    if ( ($uploads = wp_get_upload_dir()) && false === $uploads['error'] ) {
        $path   = $uploads['basedir'].'/'.$file;
        $name   = basename($path);

        if (!empty($size)) {
            $image  = image_downsize( $id, $size );
            $url    = $image[0];
            $path   = str_replace($uploads['baseurl'], $uploads['basedir'], $url);
        }

        if (is_file($path)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $path);
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            header('HTTP/1.0 200 OK');
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Content-Type: '.$mime);
            header('Content-Disposition: inline; filename='.$name.'.'.$ext);
            header("Content-Transfer-Encoding: binary");
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        }
    }
}

// final header when no file was delivered
header('HTTP/1.0 404 Not Found');
exit;