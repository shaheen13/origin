<?php
require_once 'core/init.php';

error_reporting(E_ALL & ~ E_NOTICE);

if(Input::exists() && Input::get('submit') === 'log in') {
	if(Token::check(Input::get('token'))) {
		$val = new Validation();

		$directives = array(
			'username' => array(
				'rules' =>'required'
				),
			'password' => array(
				'rules' => 'required'
				)
			);

		if($val->validate($_POST, $directives)) {
			$user = new User();

			if($user->login(Input::get('username'), Input::get('password'), Input::get('remember'))) {
				
				Session::flash('success', 'wellcome, you have logged in successfully');
				Redirect::to('index.php');
				
			} else {
				$val->addErrors($user->loginErrors());
			}
		}

		echo $val->errors_html;

	} else {
		echo "CSRF Attack Prevented";
	}
}
?>
<form action="" method="post" >

	<label for="username" >Username:</label>
	<input type="text" name="username" id="username" autocomplete="off" />

	<br />

	<label for="password" >Password:</label>
	<input type="password" name="password" id="password" />

	<br />

	<input type="checkbox" value='1' name="remember" id="remember" />
	<label for="remember" >Remember me:</label>

	<br />

	<input type="hidden" name="token" value="<?php echo Token::generate();?>" />
	<input type="submit" name="submit" value="log in" />
</form>