<?php
/*
Template Name: User Register
*/

get_header();

global $wpdb, $user_ID;

if($_POST) {

	//print2($_POST);
	$username = $wpdb->escape($_POST['username']);
	$email = $wpdb->escape($_POST['email']);
	$password = $_POST['password'];

	if($username && $email && $password) {
		$new_user_id = wp_create_user($username, $password, $email);
		if($new_user_id) {
			if ( isset( $_POST['first_name'] ) ) {
				update_user_meta($new_user_id, 'first_name', $_POST['first_name']);
			}
			if ( isset( $_POST['last_name'] ) ) {
				update_user_meta($new_user_id, 'last_name', $_POST['last_name']);
			}
			//wp_redirect(home_url().'/registered-users/');
			echo '<p align="center">Success: A New User has been registered!</p>';
		} else {
			echo '<p align="center">Error: Please enter valid details!</p>';
		}
	} else {
		echo '<p align="center">Error: Please enter valid details!</p>';
	}

}

?>
<div class="container">
    <h2>Custom User Registration Form</h2><br /><br />
    <form id="wp_signup_form" action="<?php the_permalink(); ?>" method="POST" >
        <label for="first_name">First Name: </label><input type="text" name="first_name" id="first_name" required /><br /><br />
        <label for="last_name">Last Name: </label><input type="text" name="last_name" id="last_name" required /><br /><br />
        <label for="username">Username: </label><input type="text" name="username" id="username" required /><br /><br />
        <label for="email">Email address: </label><input type="email" name="email" id="email" required /><br /><br />
        <label for="password">Password: </label><input type="password" name="password" id="password" maxlength="6" required /><br /><br />
        <!--<label for="password_confirmation">Confirm Password</label><input type="password" name="password_confirmation" id="password_confirmation" /><br /><br />-->
        <input type="submit" id="submitbtn" name="submit"value="SignUp" />
    </form>
</div>

<?php get_footer(); ?>