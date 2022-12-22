function get_users_completed_courses($user_id) {
	$user_info = get_user_meta($user_id);	
	//get all completed courses in users meta
	$resultArray = array_filter($user_info, function($key) {
	
		return strpos($key, 'course_completed_') === 0;
	
	}, ARRAY_FILTER_USE_KEY);
	//remove string on array key 
	function prepare(array $resultArray) {
	
		$result = array();	 
		foreach ($resultArray as $key => $value) {
			$key = str_replace('course_completed_', '', $key);	 
			$result[$key] = $value;
		}
	 
		return $result;
	}	
	$newArray = prepare($resultArray);	
	//output an array the courses id
	$output = array_keys($newArray);	

	return $output;

}
