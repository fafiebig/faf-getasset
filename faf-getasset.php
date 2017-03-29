<?php
/*
Plugin Name: FAF GetAsset
Plugin URI: https://github.com/fafiebig/faf-optim
Description: Media proxy to privatize media urls.
Version: 1.0
Author: F.A. Fiebig
Author URI: http://fafworx.com
License: GNU GENERAL PUBLIC LICENSE
*/
defined('ABSPATH') or die('No direct script access allowed!');

/**
 * @param $param
 * @return string
 */
function shortImageDownloadLink($param)
{
    $title  = (isset($param['title'])) ? $param['title'] : 'Download Image';
    $link   = (isset($param['link'])) ? $param['link'] : 'Download Image';
    $size   = (isset($param['size'])) ? $param['size'] : 'thumbnail';
    $class  = (isset($param['class'])) ? $param['class'] : 'class';
    $url    = getSecureAttachmentImageSrc(null, $param['id'], $size, 'attachment');

    echo '<a class="'.$class.'" href="'.$url[0].'" download="'.$url[0].'" title="'.$title.'">'.$link.'</a>';
}
add_shortcode('download_link', 'shortImageDownloadLink');

/**
 * @param $form_fields
 * @param $post
 * @return mixed
 */
function addAttachmentFields( $form_fields, $post )
{
    $private = get_post_meta($post->ID, 'private', true);
    $checked = ($private === 'yes') ? 'checked="checked"' : '';

    $form_fields['private'] = array(
        'label' => 'Privat',
        'input' => 'html',
        'html' => '
            <input type="checkbox" id="attachments-'.$post->ID.'-private" name="attachments['.$post->ID.'][private]" value="yes" '.$checked.' />
            <label for="attachments-'.$post->ID.'-private">Nur für eingeloggte Nutzer sichtbar.</label>
        ',
        'value' => $private,
        'helps' => ''
    );

    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'addAttachmentFields', 10, 2);

/**
 * @param $attachment_id
 */
function saveAttachmentFields( $id )
{
    $private = null;
    if ( isset( $_REQUEST['attachments'][$id]['private'] ) ) {
        $private = $_REQUEST['attachments'][$id]['private'];
    }

    update_post_meta( $id, 'private', $private );
}
add_action( 'edit_attachment', 'saveAttachmentFields', 10, 2 );

/**
 * @param $url
 * @return mixed
 */
function getSecureAttachmentUrl($url, $fileId, $disp = 'inline')
{
    if (!is_admin()) {
        $private = get_post_meta($fileId, 'private', true);

        if ($private === 'yes' && !wp_attachment_is_image($fileId)) {
            $assetHash = base64_encode($fileId.'||'.$disp);
            return plugin_dir_url( __FILE__ ).'asset.php?asset=' . $assetHash;
        }
    }

    return $url;
}
add_filter('wp_get_attachment_url', 'getSecureAttachmentUrl', 10, 2);

/**
 * @param $url
 * @return mixed
 */
function getSecureAttachmentImageSrc($url, $fileId, $size, $disp = 'inline')
{
    if (!is_admin()) {
        $private = get_post_meta($fileId, 'private', true);

        if ($private === 'yes') {
            $assetHash = base64_encode($fileId.'|'.$size.'|'.$disp);
            $url[0] = plugin_dir_url( __FILE__ ).'asset.php?asset='.$assetHash;
        }
    }

    return $url;
}
add_filter('wp_get_attachment_image_src', 'getSecureAttachmentImageSrc', 10, 3);

/**
 * @param $columns
 * @return array
 */
function addAttachmentColumns($columns)
{
    $retval = [];
    foreach ($columns AS $key => $col) {
        $retval[$key] = $col;
        if ($key == 'author') {
            $retval['private'] = 'Private';
        }
    }

    return $retval;
}
add_filter("manage_media_columns", "addAttachmentColumns");

/**
 * @param $column
 */
function addAttachmentColumnsValues($column)
{
    global $post;
    if ($column == 'private') {
        echo get_post_meta($post->ID, 'private', true);
    }
}
add_action("manage_media_custom_column", "addAttachmentColumnsValues");