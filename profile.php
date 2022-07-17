<?php

use App\Helpers\Auth;

require_once 'control/core/init.php';

Auth::getInstance()->require_login();

$user = Auth::getInstance()->get_user();

?>

<h1>Profile</h1>

<dl>
    <dt>Full Name</dt>
    <dd><?php echo $user->full_name; ?></dd>
    <dt>Username</dt>
    <dd><?php echo $user->username; ?></dd>
    <dt>Email</dt>
    <dd><?php echo $user->email; ?></dd>
    <dt>Joined</dt>
    <dd><?php echo date('F Y', strtotime($user->created_at)); ?></dd>
</dl>

<p>Edit profile <a href="edit.php">here</a> </p>
