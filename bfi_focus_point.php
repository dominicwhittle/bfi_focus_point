<?php

// Requires BFI thumb for interal image generation use.
if ( function_exists( 'bfi_thumb' ) ):


// Add "focus Point" link media library options (View, Edit, Delete, etc.)
add_action( 'media_row_actions', 'wknds_bfifc_editor_links', 10, 2 );
function wknds_bfifc_editor_links( $links, $post ){
  if (preg_match('/image/', $post->post_mime_type)) {
    $links['wknds_bfifc'] = '<a href="#!" class="wknds_bfifc_trigger" title="Add/Edit Image Crop Focus Point" data-id="' . $post->ID . '">Focus Point</a>';
  }
  return $links;
}



// Ajax function to update the DB with focus point.
add_action( 'wp_ajax_update_focus_point', 'wknds_update_focus_point' );
function wknds_update_focus_point(){
  global $wpdb; // this is how you get access to the database

  $post_id = intval( $_POST['post_id'] );
  $focus   = ( $_POST['focus'] );

  $focus = validate_crop_focus( $focus );

  update_post_meta( $post_id, '_wknds_focus', $focus );

  echo get_post_meta( $post_id, '_wknds_focus', true );

  die(); // this is required to return a proper result

}


// @TODO — this isn't currently used I don't think.
// Ajax function to get the DB with focus point.
add_action( 'wp_ajax_get_focus_point', 'wknds_get_focus_point' );
function wknds_get_focus_point(){
  global $wpdb; // this is how you get access to the database

  $post_id = intval( $_POST['post_id'] );

  echo validate_crop_focus( get_post_meta( $post_id, '_wknds_focus', true ) );

  die(); // this is required to return a proper result

}

// Ajax function to generate markup and return it for use in the lightbox
// popup
add_action( 'wp_ajax_get_focus_markup', 'wknds_get_focus_markup' );
function wknds_get_focus_markup(){
  global $wpdb; // this is how you get access to the database

  $img_id     = intval( $_POST['post_id'] ); // the attachment/image ID
  $img_data   = wp_get_attachment_image_src( $img_id, 'full' );
  $bfi_params = array(
    'width'  => 1024,
  );

  $classes = '';
  if ( get_crop_focus( $img_id, true ) ) // second parameter means it returns false if no saved focus point.
    $classes = 'wknds-focus-point--saved';

  list( $x, $y ) = get_crop_focus( $img_id, false ); // second param means falls back to default focus point of none saved.

  $x = ( $x*100 ) . '%';
  $y = ( $y*100 ) . '%';

  ?>

  <div id="wknds-focus-cover">

    <p id="wknds-focus-notes">
      Attachment/Image ID: <?= $img_id ?> <br>
      Focus point is saved when the indicator lights up blue.
      <span id="wknds-focus-close-alt" class="button button-primary button-small">Close</span>
    </p>

    <div id="wknds-focus-wrap">

      <img id="wknds-focus-img" src="<?= bfi_thumb( $img_data[0], $bfi_params ) ?>" alt />

      <span id="wknds-focus-point" class="<?= $classes ?>" style="left: <?= $x ?>; top: <?= $y ?>;">
        <span id="wknds-fp__indicator"></span>
      </span>

    </div>
    <!-- <span id="wknds-focus-close">&times;</span> -->
  </div>

  <?
  die(); // this is required to return a proper result

}


// Enqueue scripts and styles
add_action( 'admin_enqueue_scripts', 'wknds_crop_focus_styles' );
function wknds_crop_focus_styles() {
  wp_enqueue_style( 'wknds-focus-css', get_template_directory_uri() . '/style.css', false, '1.0.0' );
  // wp_enqueue_style( 'wknds-focus-css', plugins_url( 'style.css' , __FILE__ ), false, '1.0.0' );
  wp_enqueue_script( 'jquery-ui-draggable' );
  // wp_enqueue_script( 'wknds-focus-script', get_template_directory_uri() . '/focus/script.js', array( 'jquery' ), '', true );
  wp_enqueue_script( 'wknds-focus-script', plugins_url( 'script.js' , __FILE__ ), array( 'jquery' ), '', true );
}



function validate_crop_focus( $focus ){
  if ( ! is_array( $focus ) )
    $focus = explode( ',', $focus );

  if ( count($focus) == 2 ):
    list( $x, $y ) = $focus;

    // Despite draggable settings to limit it, it's possible to drag the loupe off the image and get invalid numbers.
    // This sucks because the loupe's position gets saved out there too and you can't drag it back.
    $x = $x > 1 ? 1 : $x;
    $x = $x < 0 ? 0 : $x;
    $y = $y > 1 ? 1 : $y;
    $y = $y < 0 ? 0 : $y;

    return $x.','.$y;

  endif;

  // Something's not right, return the default
  return '0.5,0.5';

}


// returns an array of left, top offset ready for passing into the BFI Image crop param
function get_crop_focus( $attachment_id, $false_if_not_saved = false ){
  if ( ! $attachment_id )
    return false;

  if ( $focus = get_post_meta( $attachment_id, '_wknds_focus', true ) )
    return explode( ',', $focus );

  if ( $false_if_not_saved )
    return false;

  return array( 0.5, 0.5 ); // Default crop from center if nothing is set.
}



endif; // function_exists( 'bfi_thumb' )
