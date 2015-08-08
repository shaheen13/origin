<?php

require_once 'core/init.php';

if(Input::exists() && Input::get('submit') === 'update')
{
	if(Token::check(Input::get('token')))
	{
		$val = new Validation;

		$directives = array(
			"c_password" => array(
				"rules" => "required|min_length-5|max_length-20",
				"placeholder" => "Current Password"
				),
			"n_password" => array(
				"rules" => "required|min_length-5|max_length-20",
				"placeholder" => "New Password"
				),
			"n_password_2" => array(
				"rules" => "match-n_password_2",
				"placeholder" => "Repeating Password",
				"custom_msg" => "New Password don't match in two fields"
				)
			);

		if($val->validate($_POST, $directives))
		{
			$user = new User;
			if($user->login($user->data()->username, Input::get('c_password')))
			{
				try{
					$salt = Hash::salt(32);
					$fields = array(
						"password" => Hash::make(Input::get('n_password'), $salt),
						"salt" => $salt
						);
					$user->update($fields, $user->data()->id);
					Session::flash('flash', "successfully changed password");
					Redirect::to('index.php');
				} catch(Exception $e) {
					die($e->getMessage());
				}
			}
			else
			{
				$val->addErrors($user->loginErrors());
			}
		}

		echo $val->errors_html;
	}
	else
	{
		echo "CSRF Prevented";
	}
}
?>

<form action="" method="post" >
	<label for="c_password" >Current Password:</label>
	<input type="password" name="c_password" id="c_password" />

	<br />

	<label for="n_password" >New Password:</label>
	<input type="password" name="n_password" id="n_password" />

	<br />

	<label for="n_password_2" >New Password Again:</label>
	<input type="password" name="n_password_2" id="n_password_2" />

	<br />

	<input type="hidden" name="token" value="<?=Token::generate();?>" />
	<input type="submit" name="submit" value="update" />

</form>