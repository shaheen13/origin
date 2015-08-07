<?php
require_once 'core/init.php';
// echo "<pre>";
$user = new User();

echo Session::flash('success') . "<br />";
if($user->isLoggedIn()) {
?>
<p>
	Hello <a href="profile.php?user=<?=$user->data()->username;?>" ><?=$user->data()->name;?></a>
</p>

<ul>
	<li><a href="logout.php" >Log Out</a></li>
	<li><a href="update.php" >Update</a></li>
	<li><a href="profile.php" >Profile</a></li>
	<li><a href="changepassword.php" >Change Password</a></li>
</ul>
<?php
	if($user->hasPermission('moderator')) {
		echo "you are an " . $user->data()->group;
	}
} else {
?>
<p>
you have to <a href="login.php" >login</a> or <a href="register.php" >register</a>
</p>
<?php
}