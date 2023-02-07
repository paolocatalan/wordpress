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

//member auto enroll learndash courses
function members_course_enrollement( $user_id, $feed, $entry, $user_pass ) {
    // Run only for members event registration form	
    if ( rgar( $entry, 'form_id' ) != 9 ) {
       return;
    }
	 
	$event_id = rgar( $entry, '26' );
	$course_meta_key = 'course_'. $event_id .'_access_from';
	$timestamp = date('U');	

    if ( ! empty( $event_id ) ) {		
		update_user_meta( $user_id, $course_meta_key, $timestamp );
	}
}
add_action( 'gform_user_updated', 'members_course_enrollement', 10, 4 );

// onboarding course
function members_onboarding_course( $user_id, $feed, $entry, $user_pass ) {
    // Run only for members renewal form	
    if ( rgar( $entry, 'form_id' ) != 2 ) {
       return;
    }	
	
	$timestamp = date('U');	
	//On boarding course
	update_user_meta( $user_id, 'course_49_access_from', $timestamp );		
	//membership cenrtificate
	update_user_meta( $user_id, 'course_completed_49', $timestamp );

}
add_action( 'gform_user_updated', 'members_onboarding_course', 10, 4 );

//checks the email address if it is already registered with the event for non-members form
function email_registration_check( $validation_result ) {
	$email = rgpost( 'input_3' );
	$event_name = rgpost( 'input_42' );	
	$search_criteria = array(
    	'status'        => 'active',
    	'field_filters' => array(
        	'mode' => 'all',
        	array(
            	'key'   => '3',
            	'value' => $email
        	),
        	array(
            	'key'   => '42',
            	'value' => $event_name
        	)
    	)
	);
	$form_id = 19;
	$entry_count = GFAPI::count_entries( $form_id, $search_criteria );
			
  $form = $validation_result['form'];
  
	if ( $entry_count >= 1 ) {

		$validation_result['is_valid'] = false;

		foreach( $form['fields'] as &$field ) {

			if ( $field->id == '3' ) {
				$field->failed_validation = true;
				$field->validation_message = 'This email address is already registered with this event.';
				break;
			}
		}

	}

	$validation_result['form'] = $form;
	return $validation_result;
  
}
add_filter( 'gform_validation_19', 'email_registration_check' );

// generate username for events registration and membership form
add_filter( 'gform_username_19', 'generate_username', 10, 4 );
function generate_username( $username, $feed, $form, $entry ) {
	GFCommon::log_debug( __METHOD__ . '(): running.' );
	// Update 2.3 and 2.6 with the id numbers of your Name field inputs. e.g. If your Name field has id 1 the inputs would be 1.3 and 1.6
	$fullname = strtolower( rgar( $entry, '1.3' ) . rgar( $entry, '1.6' ) );
	$username = str_replace(' ', '', $fullname);
  
	if ( empty( $username ) ) {
		GFCommon::log_debug( __METHOD__ . '(): Value for username is empty.' );
		return $username;
	}

	if ( ! function_exists( 'username_exists' ) ) {
		require_once( ABSPATH . WPINC . '/registration.php' );
	}

	if ( username_exists( $username ) ) {
		GFCommon::log_debug( __METHOD__ . '(): Username already exists, generating a new one.' );
		$i = 2;
		while ( username_exists( $username . $i ) ) {
				$i++;
		}
		$username = $username . $i;
		GFCommon::log_debug( __METHOD__ . '(): New username: ' . $username );
	};

	return $username;
}
