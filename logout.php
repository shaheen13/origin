<?php
require_once 'core/init.php';

$user = new User();

$user->logout();

Session::flash('success', 'You have loged out successfully');
Redirect::to('index.php');