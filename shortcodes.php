<?php

// Check if user is logged in, output content on the enclosing shortcode for allowed users.
function member_check_logged_in( $atts, $content = null ) {
  $user = wp_get_current_user();
  $members_only = array( 'administrator', 'subscriber' );	
  $spectators_allowed = array( 'administrator', 'subscriber', 'bbp_spectator' );	
	if($atts['partners'] == "yes") {
		if ( is_user_logged_in() && !is_null( $content ) && !is_feed() )
			if ( array_intersect( $spectators_allowed, $user->roles ) ) :
				return do_shortcode($content);		
			endif;
	} else {
	 	if ( is_user_logged_in() && !is_null( $content ) && !is_feed() )		
			if ( array_intersect( $members_only, $user->roles ) ) :
				return do_shortcode($content);						
			endif;
	}
}		
add_shortcode( 'members_only', 'member_check_logged_in' );

?>
