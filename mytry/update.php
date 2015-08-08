<?php
require_once 'core/init.php';

$user = new User;

if($user->isLoggedIn())
{
	if(Input::exists() && Input::get('submit') === "update")
	{
		if(Token::check(Input::get('token')))
		{
			$val = new Validation;

			$directives = array(
				"name" => array(
					"rules" => "required|max_length-20|min_length-5",
					"placeholder" => "your new name"
					)
				);

			if($val->validate($_POST, $directives))
			{
				try {
					$fields = array(
						"name" => Input::get('name')
						);
					$user->update($fields, $user->data()->id);

					Session::flash('flash', "updated sucessfully");
					Redirect::to('index.php');
				} catch(Exception $e) {
					die($e->getMessage());
				}
			}

			echo $val->errors_html;
		}
		else
		{
			echo "CSRF prevented";
		} 
	}
}
else
{
	Session::flash('flash', "you don't have permission");
	Redirect::to('index.php');
}
?>
<form action="" method="post" >
	<label for="name" >Your New Name:</label>
	<input type="text" name="name" id="name" value="<?=$user->data()->name;?>" />

	<br />

	<input type="hidden" name="token" value="<?=Token::generate();?>" />
	<input type="submit" name="submit" value="update" />
</form>