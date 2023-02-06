<?php

//populates form fields with post meta data from query string
function populate_event_id( $value ) {
	$id = get_the_ID();
	$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : $id;
	$value = $event_id;
   	
	return $value;
}
add_filter( 'gform_field_value_event_id', 'populate_event_id' );

function populate_event_type( $value ) {
	$id = get_the_ID();
	$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : $id;
	$value = $event_id;
	$terms = get_the_terms( $event_id, 'ld_course_category' );
	foreach ( $terms as $term ) {
		if( $term->parent == 105 ) { 
			$value = $term->name;
		}	
	}
		
	return $value;
}
add_filter( 'gform_field_value_event_type', 'populate_event_type' );

function populate_event_title( $value ) {
	$id = get_the_ID();
	$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : $id;
	$event_info = get_post( $event_id );
	$value = $event_info->post_title;		
		
	return $value;
}
add_filter( 'gform_field_value_event_title', 'populate_event_title' );

function populate_event_date( $value ) {
	$id = get_the_ID();
	$event_id = isset($_GET['event_id']) ? $_GET['event_id'] : $id;
	$event_date = get_post_meta( $event_id, '_start_eventtimestamp', true );
	$value = date('l, j F Y, g:i a', strtotime($event_date))." (GMT +8)";
	
	return $value;
}
add_filter( 'gform_field_value_event_date', 'populate_event_date' );
