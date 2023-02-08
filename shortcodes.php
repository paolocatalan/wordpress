<?php

// Pass Data Query String Paramenters
function get_url_parameter($atts) {  
	$atts = shortcode_atts( array(
    'parameter_name' => '',
  ), $atts );
  return $_GET[$parameter_name];  
}
add_shortcode('pass_data', 'get_url_parameter');

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

//display content for non members
function visitor_check_logged_in($atts, $content = null) {
	$user = wp_get_current_user();
	 if ( ( !is_user_logged_in() && !is_null( $content ) ) || is_feed() ) {
		return do_shortcode($content); 
	 } else {		
		return; 		 
	 } 
	
}
add_shortcode( 'non-members', 'visitor_check_logged_in' );

// Output LearnDash course meta data via global id or parameter name
function get_course_details( $atts ) {
	$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : 0;
	$course_id = get_the_ID();
	
	// Get Event ID via query string paramenter, if false get the post ID
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

function default_login_form($atts) {
	$atts = shortcode_atts( array(
		'label_password' => 'Password',
		'label_button' => 'Login',
		'redirect_to'=> get_permalink( get_the_ID() )
	), $atts );

  $args = array(
		'echo'						=> false,
		'label_username' 	=> __( 'Email Address' ),
		'label_password' 	=> $atts['label_password'],
		'label_log_in'		=> $atts['label_button'],	
		'redirect'				=> $atts['redirect_to'],
		'remember'				=> false,
		'value_remember'	=> false,
  );
  
  return wp_login_form( $args );
 
}
add_shortcode( 'login_form', 'default_login_form' );

// Navigation header with menu options and navbar class
function navigation_header($atts) {
	$atts = shortcode_atts( array(
		'class' => 'navbar-dark bg-dark',
		'navbar_brand' => '',
		'menu' => ''
	), $atts);	

	if ($atts['navbar_brand'] == 'true') {
		$brand = '<a class="navbar-brand" href="'. network_site_url( '/' ) .'" title="'. get_bloginfo( 'name' ) .'"><img src="https://www.parima.org/wp-content/uploads/2019/11/PARIMAlogo_PNG-White.png" alt="PARIMA" loading="eager" width="145" height="33" /></a>';
	} 

	if ( !empty($atts['menu']) ) {
		$nav_menus = wp_nav_menu(
			array(
				'menu'    		  	=> $atts['menu'],
				'menu_id'       	=> false,
				'menu_class'   		=> 'navbar-nav',
				'depth'         	=> 3,
				'echo'          	=> false,								
				'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
				'walker'          => new WP_Bootstrap_Navwalker(),
			)
		);
	} else {
		global $post;	
		/**Add menu link to Dashboard and Login page */
		if ( is_user_logged_in() ) {			
			$login_link = '<a class="nav-link" href="' . site_url('/dashboard/') . '">Dashboard</a>';	
		} else {		
			$login_link = '<a class="nav-link" href="/login/?redirect=' . $post->post_name . '">Login</a>';				
		}	
		/**Add the navigation menu */
		switch ($post->post_name) {
			case 'country-focus':
				$nav_menus = '<ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="/conference-registration/">Singapore 2022</a></li>' . $login_link . '</ul>';
				break;
			case 'conference-registration':
				$nav_menus = '<ul class="navbar-nav"><li class="nav-item">' . $login_link . '</ul>';
				break;
			default:
				$nav_menus = '<ul class="navbar-nav"><li class="nav-item">' . $login_link . '</li></ul>';
		}
		
	}
	/**Add navigation Call to Action */
	if ( $menu == 'refactor-event-page' ) {			
		$nav_cta = '<a class="btn btn-primary mx-2 disabled" href="/registration/" disabled>Register Now</a>';			
	}

	return '<nav class="navbar navbar-expand-lg ' . $atts['class'] . '" role="navigation">
						<div class="container-xl">
							'. $brand .'
							<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon"></span>
							</button>
							<div id="navbarCollapse" class="collapse navbar-collapse justify-content-end">  
							' . $nav_menus . $nav_cta . '
							</div>	
						</div>  
					</nav>';

}
add_shortcode('navbar', 'navigation_header');
