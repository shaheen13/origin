<?php
require_once 'core/init.php';

error_reporting(E_ALL & ~ E_NOTICE);

if(Input::exists() && Input::get('submit') === "register")
{
	if(Token::check(Input::get('token'))) {
		$val = new Validation();

		$directives = array(
			"username"=>array(
				"rules" => "required|max_length-20|min_length-5|unique-users.username",
				"placeholder" => "Username"
				),
			"name"=>array(
				"rules" => "required|max_length-15|min_length-5",
				"placeholder" => "Your name"
				),
			"password"=>array(
				"rules" => "required|max_length-20|min_length-5",
				"placeholder" => "Password"
				),
			"password_2"=>array(
				"rules" => "required|match-password",
				"placeholder" => "Repeating password",
				"custom_msg" => "Password don't match in two fields"
				)
			);

		if($val->validate($_POST, $directives))
		{
			$user = new User();

			$salt = Hash::salt('32');

			try {

				$user->create(array(
					'username' => Input::get('username'),
					'password' => Hash::make(Input::get('password'), $salt),
					'salt'	   => $salt,
					'name'	   => Input::get('name'),
					'joined'   => date('Y-m-d H:i:s'),
					'user_group'	   => 1
					));

				Session::flash('success', 'you have registered successfully!');
				Redirect::to('index.php');

			} catch(Exception $e) {

				die($e->getMessage());
				
			}
		}
		else
		{
			echo $val->errors_html;
		}
	} else {
		echo "cross site request forgery failed";
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Register</title>
	</head>
	<body>
		<form action="<?=htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post" >
			<label for="username">Username:</label>
			<input type="text" name="username" id="username" autocomplete="off" value="<?php echo escape(Input::get('username'));?>" />
			
			<br />

			<label for="name" >Your Name:</label>
			<input type="text" name="name" id="name" autocomplete="off" value="<?php echo escape(Input::get('name'));?>" />

			<br />

			<label for="password" >Choose Password:</label>
			<input type="password" name="password" id="password" />

			<br />

			<label for="password_2" >Verify Password:</label>
			<input type="password" name="password_2" id="password_2" />
			
			<br />

			<input type="hidden" name="token" value="<?php echo Token::generate();?>" />

			<input type="submit" name="submit" value="register" />
		</form>
	</body>
</html>