<?php

use App\Helpers\Auth;
use App\Helpers\Misc\Session;

require_once 'control/core/init.php';

//echo '<pre>' . var_export(Auth::getInstance()->is_admin(), true) . '</pre>';

?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
</head>
<body>

<h1>Startup App</h1>

<?php

if (Session::exists('success')) {
    echo Session::flash('success');
}

?>

<?php if (Auth::getInstance()->isLoggedIn()): ?>
    <p>Welcome back User <?php echo Auth::getInstance()->get_user()->username; ?> </p>

    <ul>
    <?php if (Auth::getInstance()->is_admin()): ?>
        <li><a href="#">Admin</a></li>
    <?php else: ?>
        <li><a href="#">normal user</a></li>
    <?php endif; ?>
        <li><a href="logout.php">logout</a></li>
    </ul>

<?php else: ?>
    <ul>
        <li><a href="register.php">Sign up</a></li>
        <li><a href="login.php">Log in</a></li>
    </ul>
<?php endif; ?>


</body>
</html>

