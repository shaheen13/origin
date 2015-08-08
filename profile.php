<?php

require_once 'core/init.php';

if(!$username = Input::get('user')) {
	Redirect::to('index.php');
} else {
	$user = new User(Input::get('user'));

	if($user->__construct(Input::get('user'))) {
		?>
		<h3><?=$user->data()->username;?></h3>
		<p> Full Name: <?=$user->data()->name;?></p>
		<?php
	} else {
		Redirect::to(404);
	}
}