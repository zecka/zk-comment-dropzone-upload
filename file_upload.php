<?php
function zk_check_upload_files($varname='files'){
	$check=zk_upload_files($varname,true);
	return $check;
}
function zk_upload_files($varname='files', $check=false){
/* THANK YOU TO CARLO FONTANOS: http://carlofontanos.com/ajax-multi-file-upload-in-wordpress-front-end/ */
    $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg", "pdf"); // Supported file types
    $max_file_size = 15 * 1024 * 1024; // fist number is MB
    $max_image_upload = 20; // Define how many images can be uploaded to the current post
    $wp_upload_dir = wp_upload_dir();
    $path = $wp_upload_dir['path'] . '/';
    $count = 0;

    $attachments = get_posts( array(
        'post_type'         => 'attachment',
        'posts_per_page'    => -1,
        'exclude'           => get_post_thumbnail_id() // Exclude post thumbnail to the attachment count
    ) );

    // Image upload handler
    if( $_SERVER['REQUEST_METHOD'] == "POST" ){
        
        // Check if user is trying to upload more than the allowed number of images for the current post
        if( ( count( $_FILES[$varname]['name'] ) ) > $max_image_upload ) {
            $upload_message[] = "Sorry you can only upload " . $max_image_upload . " images for each Ad";
        } else {
            $myfilesarray=array();
            foreach ( $_FILES[$varname]['name'] as $f => $name ) {
                $extension = pathinfo( $name, PATHINFO_EXTENSION );
                // Generate a randon code for each file name
                
          

                $clean_name = str_replace('.'.$extension, '', $name);
                
                $clean_name=sanitize_title($clean_name);
                
                $new_filename = $clean_name.'-'.cvf_td_generate_random_code( 10 )  . '.' . $extension;
                
                if ( $_FILES[$varname]['error'][$f] == 4 ) {
                    continue; 
                }
                
                if ( $_FILES[$varname]['error'][$f] == 0 ) {
                    // Check if image size is larger than the allowed file size
                    if ( $_FILES[$varname]['size'][$f] > $max_file_size ) {
                        $upload_message[] = "$name is too large!.";
                        continue;
                    
                    // Check if the file being uploaded is in the allowed file types
                    } elseif( ! in_array( strtolower( $extension ), $valid_formats ) ){
                        $upload_message[] = "$name is not a valid format";
                        continue; 
                    
                    } else{ 
                        // If no errors, upload the file...
                        if( move_uploaded_file( $_FILES[$varname]["tmp_name"][$f], $path.$new_filename )) {
                            
                            $count++; 
                            
							if($check!=true){
	                            $filename = $path.$new_filename;
	                            $filetype = wp_check_filetype( basename( $filename ), null );
	                            $wp_upload_dir = wp_upload_dir();
	                            $attachment = array(
	                                'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
	                                'post_mime_type' => $filetype['type'],
	                                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
	                                'post_content'   => '',
	                                'post_status'    => 'inherit'
	                            );
	                            // Insert attachment to the database
	                            $attach_id = wp_insert_attachment( $attachment, $filename);
	
	                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
	                            
	                            // Generate meta data
	                            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename ); 
	                            wp_update_attachment_metadata( $attach_id, $attach_data );
	                            $myfilesarray[]['file']=$attach_id;
	                            $last_file_id=$attach_id;
                            }
                        }
                    }
                }
            }
        }
    }
    // Loop through each error then output it to the screen
    if ( isset( $upload_message ) ) :
        foreach ( $upload_message as $msg ){        
            $files_return['message']='<p class="bg-danger">'.$msg.'</p>';
            $files_return['success']=false;
        }
    endif;
    
    // If no error, show success message
    if( $count != 0 ){
       $files_return['message']='<p class = "bg-success">'.$count.' files added successfully!</p>';
       $files_return['files_array']=$myfilesarray;
        $files_return['last_file_id']=$last_file_id;
       $files_return['success']=true;
    }
    


		return $files_return;
}



// Random code generator used for file names.
function cvf_td_generate_random_code($length=10) {
 
   $string = '';
   $characters = "23456789ABCDEFHJKLMNPRTVWXYZabcdefghijklmnopqrstuvwxyz";
 
   for ($p = 0; $p < $length; $p++) {
       $string .= $characters[mt_rand(0, strlen($characters)-1)];
   }
 
   return $string;
 
}