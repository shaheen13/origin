<?php
require_once 'core/init.php';
error_reporting(E_ALL & ~ E_NOTICE);
$user = new User();

if(!$user->isLoggedIn()) {
	Redirect::to('index.php');
}

if(Input::exists() && Input::get('submit') === 'Change') {
	if(Token::check(Input::get('token'))) {
		$val = new Validation();

		$directives = array(
			"c_password" => array(
				"rules" => "required",
				"placeholder" => "The Old password"
				),
			"n_password" => array(
				"rules" => "required|max_length-15|min_length-5",
				"placeholder" => "New Password"
				),
			"n_password_2" => array(
				"rules" => "match-n_password",
				"custom_msg" => " The twp passords dont matches"
				)
			);

		if($val->validate($_POST, $directives)) {
			if($user->login($user->data()->id, Input::get('c_password'))) {
				try {
					$fields = array(
						"password" => Hash::make(Input::get('n_password'), $user->data()->salt)
						);
					$user->update($fields, $user->data()->id);
					Session::flash('success', 'successfully changed password');
					Redirect::to('index.php');
				} catch (Exception $e) {
					die($e->getMessage());
				}

			} else {
				$val->addErrors($user->loginErrors());
			}
		}

		echo $val->errors_html;
		
	} else {
		echo "CSRF attack stopped"; 
	} 
}
?>

<form action="" method="post">
	<label for="c_password">Current Password:</label>
	<input type="password" name='c_password' id="c_password" autocomplete="off" />

	<br />

	<label for="n_password"> New Password:</label>
	<input type="password" name="n_password" id="n_password" />

	<br />

	<label for="n_passwprd_2">New Password Again:</label>
	<input type="password" name="n_password_2" id="n_password_2" />

	<br />

	<input type="hidden"  name="token" value="<?=Token::generate();?>" />

	<input type="submit" name="submit" value="Change" />
</form>