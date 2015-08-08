<?php

require_once 'core/init.php';

echo Session::flash('flash');
$user = new User();

if($user->isLoggedIn()) {

?>
<h3><a href="profile.php?user=<?=$user->data()->username;?>"><?=$user->data()->name;?></a></h3>

<ul>
	<li><a href="logout.php">Logout</a></li>
	<li><a href="update.php">Update Profile</a></li>
	<li><a href="changepassword.php">Change Password</a></li>
</ul>

<?php
} else {
	?>
<p>you have to <a href="login.php">LOGIN</a> or <a href="register.php">REGISTER</a> </p>
	<?php
}