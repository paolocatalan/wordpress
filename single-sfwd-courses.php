<?php
/*
 * Template Name: Course Template
 *  
 */
$user = wp_get_current_user();
$allowed_roles = array( 'administrator', 'subscriber' ); 

$user_id = get_current_user_id();
$course_id = get_the_ID();
$has_access = sfwd_lms_has_access($course_id, $user_id);
$featured_img_url = get_the_post_thumbnail_url($course_id, 'full');
$course_points = get_post_meta($course_id, 'course_points', true);
$location = get_post_meta($course_id, 'location', true);
$redirect_to = get_post_meta($course_id, 'redirect', true);
$url_endpoint = get_post_meta($course_id, 'url_endpoint', true); // custom field for event with landing page and external registration form
$event_start_time = get_post_meta($course_id, '_start_eventtimestamp', true);
$today = date('YmdHi');

get_header(); ?>
<main id="primary" class="content-area col-sm-12">

	<header class="entry-header">
		<div class="container-xl">
			<div class="row">
				<div class="col-12 col-sm-6 bg-indigo d-flex flex-column pt-5 pb-5">
					<?php the_title('<h1 class="mb-auto text-white text-uppercase text-center">', '</h1>'); ?>
					<div class="row">
						<div class="col-6 text-center">
							<p class="text-white text-uppercase text-xl"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-calendar2-event" viewBox="0 0 16 16"><path d="M11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z"/><path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4z"/></svg> <?php echo date('j F', strtotime($event_start_time)); ?></p>
						</div>
						<div class="col-6 text-center">
							<p class="text-white text-uppercase text-xl"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16"><path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/><path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg> <?php echo empty($location) ? "Asia Pacific" : $location; ?></p>
						</div>
						<div class="col-6 d-flex justify-content-center align-self-center">
							<?php
							if ($today > $event_start_time) {
							
								echo '<button type="button" class="btn btn-primary" disabled>Concluded</button>';
							
							} else {

								if ( is_user_logged_in() ) {

									// we can check if members are enrolled for end point url
									if ($has_access) {
								
										echo '<button type="button" class="btn btn-success text-uppercase">Enrolled</a>';
								
									} else {

										// end point URL for landing page with extenal form
										if ($url_endpoint) {

											echo '<a href="' . $url_endpoint . '" class="btn btn-success text-uppercase">Enrol Now</a>';

										} else if ($redirect_to) {

											echo '<a href="' . $redirect_to . '" class="btn btn-success text-uppercase">Enrol Now</a>';

										} else {

											?>
											<script>
												function spinner() {
													document.getElementById('loader').insertAdjacentHTML('afterbegin', '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
												}
											</script>
											<?php
											if ( array_intersect( $allowed_roles, $user->roles ) ) {
												gravity_form(9, false, false, false, '', true, 12);
												echo '<button onclick="spinner();document.getElementById(\'gform_submit_button_9\').click();" id="loader" class="btn btn-success text-uppercase" type="button"> Enrol Now</button>';
											}	
										}
									}
								} else {

									if ($url_endpoint) {
										echo '<a href="' . $url_endpoint . '" class="btn btn-success text-uppercase">Enrol Now</a>';
									} else {
										// check if analytics can track links with end point url
										echo '<a href="/registration/?event_id=' . $course_id . '" class="btn btn-success text-uppercase">Enrol Now</a>';
									}
								}
							}
							?>
						</div>
						<div class="col-6 d-flex justify-content-center align-self-center">
							<?php
							if ( array_intersect( $allowed_roles, $user->roles ) ) {
								if ($course_points) {
									echo '<button class="btn btn-outline-light"> ' . $course_points . ' CPD Points</button>';
								}
							}
							?>
						</div>
					</div>
				</div>
				<div class="col-12 col-sm-6 px-0" style="background: url(<?php echo $featured_img_url; ?>) no-repeat center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover; min-height:300px;"></div>
			</div>
		</div>
	</header><!-- .entry-header -->
	<?php
	if (has_term(array('risk-technical-skills', 'business-knowledge', 'relationship-management', 'judgement-and-decision-making', 'learning-agility'), 'ld_course_category')) {

		echo '<div class="container-xl mt-4 mb-5 px-0">';

		$terms = get_the_terms($course_id, 'ld_course_category');
		foreach ($terms as $risk_technical_skills) {
			if ($risk_technical_skills->parent == 18) {
				echo '<button class="btn btn-indigo btn-sm mx-1">' . $risk_technical_skills->name . '</button>';
			}
		}
		foreach ($terms as $business_knowledge) {
			if ($business_knowledge->parent == 23) {
				echo '<button class="btn btn-green btn-sm mx-1">' . $business_knowledge->name . '</button>';
			}
		}
		foreach ($terms as $relationship_management) {
			if ($relationship_management->parent == 37) {
				echo '<button class="btn btn-purple btn-sm mx-1">' . $relationship_management->name . '</button>';
			}
		}
		foreach ($terms as $judgement_decision_making) {
			if ($judgement_decision_making->parent == 44) {
				echo '<button class="btn btn-unique btn-sm mx-1">' . $judgement_decision_making->name . '</button>';
			}
		}
		foreach ($terms as $learning_agility) {
			if ($learning_agility->parent == 48) {
				echo '<button class="btn btn-amber btn-sm mx-1">' . $learning_agility->name . '</button>';
			}
		}

		echo '</div>';
	}
	?>

	<?php
	while (have_posts()) : the_post();

		get_template_part('template-parts/content', 'notitle');

	endwhile; // End of the loop.
	?>

</main><!-- #main -->
<?php
get_footer();
