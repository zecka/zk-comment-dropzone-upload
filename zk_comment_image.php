<?php
/*
Plugin Name: ZK COMMENT DROPZONE UPLOAD
Version: 0.0.1
Description: Demos DropzoneJS in WordPress
Author: Robin Ferrari
GitHub Plugin URI: zeckart/zk-comment-dropzone-upload
GitHub Plugin URI: https://github.com/zeckart/zk-comment-dropzone-upload
*/

define( 'ZKCI_URL', substr(plugin_dir_url( __FILE__ ), 0, -1) );
define( 'ZKCI_PATH', substr(plugin_dir_path( __FILE__ ), 0, -1) );
define( 'ZKCI_VERSION', '1.1' );

include(ZKCI_PATH.'/file_upload.php');

add_action( 'plugins_loaded', 'dropzonejs_init' );
function dropzonejs_init() {
	add_action( 'wp_enqueue_scripts', 'dropzonejs_enqueue_scripts' );
	add_shortcode( 'dropzonejs', 'dropzonejs_shortcode' );
}

function dropzonejs_enqueue_scripts() {

	wp_enqueue_script(
		'dropzonejs',
		'https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js',
		array(),
		ZKCI_VERSION
	);
	
	// Load custom dropzone javascript
	wp_enqueue_script(
		'customdropzonejs',
		ZKCI_URL. '/customize_dropzonejs.js',
		array( 'dropzonejs' ),
		ZKCI_VERSION
	);
	

	
	$drop_param = array(
	  'upload'=>admin_url( 'wp_ajax.php?action=handle_dropped_media' ),
	  'delete'=>admin_url( 'wp_ajax.php?action=handle_deleted_media' ),
	);
	wp_localize_script('customdropzonejs','dropParam', $drop_param);
	
	wp_enqueue_style(
		'dropzonecss',
		'https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.css',
		array(),
		ZKCI_VERSION
	);
}


add_filter('comment_form', function($output) {
    return str_replace('<form', '<form enctype="multipart/form-data', $output);
});

function zk_comment_form($options=array()){
    ob_start();
    comment_form($options);
    echo str_replace('<form','<form enctype="multipart/form-data" ',ob_get_clean());
}

add_action( 'comment_form_logged_in_after', 'additional_fields' );
add_action( 'comment_form_after_fields', 'additional_fields' );

function additional_fields () {


 echo '<div id="media-uploader" class="dropzone">
  	    <div class="dz-default dz-message">Déposez vos images ici ou  <a href="#">Choisir des images</a></div>
  	    <div class="fallback">
  			<input name="file" type="file" multiple />
  		</div>
	  </div>
	  <input type="file" name="zk_comment_image_35[]" id="zk_comment_image_35" multiple="multiple">
	  <input type="text" name="phone">'; 
	  
	/* echo '<div id="media-uploader" class="dropzone"></div>
<input type="hidden" name="media-ids" value="">'; */

}

add_filter( 'wp_insert_comment', 'zk_save_comment_image' );
function zk_save_comment_image( $comment_id ){
	if ( ! isset( $_POST['rating'] ) ){
		$post_id=$_POST['comment_post_ID'];
		$comment_image_id="zk_comment_image_35";
		if ( isset( $_FILES[ $comment_image_id ] ) && ! empty( $_FILES[ $comment_image_id ]['name'][0] ) ) {
			add_comment_meta( $comment_id, 'test', 'fileis' );
			$images=zk_upload_files($comment_image_id);
			if($images['success']==true){
				add_comment_meta( $comment_id, 'comment_images', $images['files_array']);
			}
			add_comment_meta( $comment_id, 'message_image', $images['message']);
	
		}else{
			add_comment_meta( $comment_id, 'test', 'fileisNOT' );
		}
		
	}
	else{
		
	}
	
	echo print_r($_FILES);
		
		
}





//add_filter( 'preprocess_comment', 'verify_comment_meta_data' );
function verify_comment_meta_data( $commentdata ) {
	$comment_image_id="zk_comment_image_35";
	
	if ( isset( $_FILES[ $comment_image_id ] ) && ! empty( $_FILES[ $comment_image_id ]['name'][0] ) ) {
		$check=zk_check_upload_files($comment_image_id);
		if($check['success']==false){
			wp_die($check['message']);

		}
	
	}

	return $commentdata;

}