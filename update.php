<?php
require_once 'core/init.php';
error_reporting(E_ALL & ~ E_NOTICE);
$user = new User();

if(Input::exists() && Input::get('submit') === "Update") {

	if(Token::check(Input::get('token'))) {
		$val = new Validation();
		$directives = array(
			"name"=>array(
				"rules" => "required|max_length-15|min_length-5",
				"placeholder" => "Your name"
				)
			);

		if($val->validate($_POST, $directives)) {
			try{
				$user = new User();

				$fields = array(
					'name' => Input::get('name')
					);
				$user->update($fields, $user->data()->id);
				Session::flash('success', 'your profile have been updated successfully ' . $user->data()->name . '!');
				Redirect::to('index.php');
			} catch(Exception $e) {
				die($e->getMessage());
			}
		} else {
			echo $val->errors_html;
		}
	} else {
		echo "csrf prevented";
	}

} else {
	echo "no submition";
}
?>
<form action="" method="post" >
	<label for="name" >New Name </label>
	<input type="text" id="name" name="name" value="<?=$user->data()->name;?>" />

	<input type="hidden" name="token" value="<?=Token::generate();?>" />

	<br />

	<input type="submit" name="submit" value="Update" />
</form>