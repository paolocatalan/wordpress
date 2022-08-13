<?php

// Check if user is logged in, output content on the enclosing shortcode for allowed BuddyPress users.
function logged_in_member_check( $atts, $content = null ) {
	$user = wp_get_current_user();
	$members_only = array( 'administrator', 'subscriber' );	
  	$spectators_allowed = array( 'administrator', 'subscriber', 'bbp_spectator' );	
	
	// Allow BuddyPress spectators to access the content
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
add_shortcode( 'members_only', 'logged_in_member_check' );

// Output LearnDash course meta data via global id or parameter name
function get_course_details( $atts ) {
	$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : 0;
	$course_id = get_the_ID();
	
	// Get event id from query string paramenter Event ID, if false get the post ID
	if ( $event_id == 0 ) {
		$id = $course_id;
	} else {
		$id = $event_id;
	}		
	$event_title = get_the_title ( $id );
	$event_img_url = get_the_post_thumbnail_url( $id, 'full' ); 
	$event_venue = get_post_meta( $id, 'location', true);	
	$course_points = get_post_meta( $id, 'course_points', true);	
	$event_start_time = get_post_meta( $id, '_start_eventtimestamp', true );
	$event_timing = date('l, F j Y, g:i a', strtotime($event_start_time)) ." (GMT +8)";
	$event_day = date('j F', strtotime($event_start_time));	
	
	// Child of Parent Course Category, LearnDash taxonomy
	$terms = get_the_terms( $id, 'ld_course_category' );	
	foreach ( $terms as $term ) {
		if( $term->parent == 105 ) { 
			$event_type = $term->name;
		}
	}
	
	if($atts['field'] == "title") {
		return $event_title;		
	} elseif($atts['field'] == "type") {
		return $event_type;	
	} elseif($atts['field'] == "image") {
		return $event_img_url;		
	} elseif($atts['field'] == "course_points") {
		return $course_points;			
	} elseif($atts['field'] == "venue") {
		return $event_venue;			
	} elseif($atts['field'] == "date") {
		return $event_timing;	
	} elseif($atts['field'] == "day") {
		return $event_day;									
	}			
		
}
add_shortcode('event_details', 'get_course_details');
?>
