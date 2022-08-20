<?php
/**
 * Plugin Name:       Cat Gallery
 * Plugin URI:        https://github.com/paolocatalan/wordpress/
 * Description:       Plugin that fetch cats and render them in a gallery.
 * Version:           1.0.3
 * Author:            Paolo Catalan
 * Author URI:        https://paolocatalan.com/
 */
   
function render_cats_from_api( $atts ) {  

	wp_register_style( 'catgallery-styles',  plugin_dir_url( __FILE__ ) . 'css/style.css' );
	wp_enqueue_style( 'catgallery-styles' );
	 
	extract(shortcode_atts(array(
		'number'     => '10',
	), $atts));
	
	
	$data = file_get_contents('https://api.thecatapi.com/v1/breeds?api_key=9f06abd1-c0b7-49cb-837c-2e49dc4b7871'); 
	$dataj = json_decode($data);

	foreach ( $dataj as $i => $cats ) {
	
  		$output .= '
			<div class="gallery"><a href="#img' . $i . '"><img src="' . $cats->image->url . '"></a></div>
			<a href="#" class="lightbox" id="img' . $i . '"><span style="background-image: url(' . $cats->image->url . ');"></span></a>
		';	
	
		if ($i >= $number - 1) break;

	}
	
return $output;

}
add_shortcode('cat_gallery', 'render_cats_from_api');
