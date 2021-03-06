<?php
require_once 'core/init.php';
if(Input::exists() && Input::get('submit') === 'register')
{
	if(Token::check(Input::get('token')))
	{
		$val = new Validation;

		$directives = array(
			"username" => array(
				"rules" => "required|max_length-20|min_length-5|unique-users.username",
				"placeholder" => "Username"
				),
			"name" => array(
				"rules" => "required|max_length-20|min_length-5",
				"placeholder" => "Your name"
				),
			"password" => array(
				"rules" => "required|min_length-5",
				"placeholder" => "Password"
				),
			"password_2" => array(
				"rules" => "required|match-password_2",
				"placeholder" => "Repeating Password",
				"custom_msg" => "password don't match in two fields"
				)
			);

		if($val->validate($_POST, $directives))
		{
			try {
				$user = new User;
				$salt = Hash::salt(32);
				$fields = array(
					"username"   => Input::get('username'),
					"password"   => Hash::make(Input::get('password'), $salt),
					"salt"       => $salt,
					"name"       => Input::get('name'),
					"joined"     => date("Y-m-d H:i:s"),
					"user_group" => 2

					);
				$user->create($fields);

				Session::flash('flash', "you have registered successfully");

				$user->login(Input::get('username'), Input::get('password'));

				Redirect::to('index.php');

			} catch (Exception $e) {

				die($e->getMessage);

			}
		}

		echo $val->errors_html;
	} else {
		echo "CSRF prevented";
	}
}
?>

<form action="" method="post">
	<label for="username">UserName:</label>
	<input type="text" name="username" id="username" value="<?=Input::get('username');?>" />

	<br />

	<label for="name" >Your Name: </label>
	<input type="text" name="name" id="name" value="<?=Input::get('name');?>" />
	
	<br />

	<label for="password" >Password:</label>
	<input type="password" name="password" id="password" />

	<br />

	<label for="password_2" >Repeat Password:</label>
	<input type="password" name="password_2" id="password_2" />

	<br />

	<input type="hidden" name="token" value="<?=Token::generate();?>" />
	<input type="submit" name="submit" value="register" />
</form>