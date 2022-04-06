<?php

class TemplatePostCode {

  function create() {
    $page = array (
      'post_title' 	=> 'Postcode',
      'post_content'=> 'postcode',
      'post_status' => 'publish',
      'post_author' => 1,
      'post_type' 	=> 'page',
      'meta_input'  => array( "_postcode" => "postcode" )
    );
    wp_insert_post ( $page );
    
  }
  
  function delete() {
    $pages = get_pages ( array('meta_key' => '_postcode') );
    foreach ( $pages as $page ) {
      wp_delete_post( $page->ID, true);
    }
  }
  
  function postcode_page( $template ) {
    if ( is_page( 'Postcode' )  ) {
        $new_template = ( WP_PLUGIN_DIR . '/fsbano/templates/postcode-page-template.php' );
    if ( '' != $new_template ) {
        return $new_template ;
    }
    }
    return $template;
  }

}

?>