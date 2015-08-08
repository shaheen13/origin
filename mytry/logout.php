<?php
require_once 'core/init.php';

$user = new User;

$user->logout();

Session::flash('flash', 'you have logged out successfully');
Redirect::to('index.php');
?>
