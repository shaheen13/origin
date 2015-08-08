<?php

require_once 'core/init.php';

if(Input::exists() && Input::get('submit') === "login") {
	if(Token::check(Input::get('token'))) {
		$val = new Validation;

		$directives = array(
			'username' => array(
				'rules' => 'required'
				),
			'password' => array(
				'rules' => 'required'
				)
			);

		if($val->validate($_POST, $directives)) {
			$user = new User;

			if($user->login(Input::get('username'), Input::get('password'), Input::get('remember'))) {
				Session::flash('flash', 'wellcome back ' . $user->data()->name);
				Redirect::to('index.php');
			} else {
				$val->addErrors($user->loginErrors());
			}

		}
		echo $val->errors_html;
	} else {
		echo "CSRF prevented";
	}
} 
?>
<form action="" method="post" >
	<label for="username" >User Name:</label>
	<input type="text" name="username" id="username" value="<?=Input::get('username');?>" />

	<br />

	<label for="password" >Password </label>
	<input type="password" name="password" autocomplete="off" />

	<br />

	<label for="remember">Remember Me</label>
	<input type="checkbox" name="remember" id="remember" value="1" />

	<br />

	<input type="hidden" name="token" value="<?=Token::generate();?>" />
	<input type="submit" name="submit" value="login" />
</form>