<?php
/**
 * Project: startup
 * File: login.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 12/10/2019 2:15 PM
 *
 * Contact: princetunes@gmail.com
 *
 */

use App\Helpers\Auth;
use App\Helpers\Misc\Redirect;
use App\Helpers\Misc\Session;

require_once 'control/core/init.php';

Auth::getInstance()->require_guest();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = array_map('trim', $_POST);

    $remember = isset($form['remember']) ? true : false;
    if (Auth::getInstance()->login($form['user_identifier'], $form['password'], $remember)) {
        if (Session::exists('return_to')) {
            $url = Session::get('return_to');
            Session::delete('return_to');
        } else {
            $url = '/startup';
        }

        Session::flash('success', 'login Successful');
        Redirect::to($url);
    }
}

?>


<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<h1>Log in</h1>

<?php
if (isset($form['user_identifier'])) {
    echo 'Invalid login<br>';
}
if (Session::exists('login')) {
    echo Session::flash('login');
}
?>

<form method="post">

    <div>
        <label for="username">Identifier</label>
        <input type="text" name="user_identifier" value="<?php echo $form['user_identifier'] ?? ''; ?>" placeholder="username or email">
    </div>

    <div>
        <label for="password">password</label>
        <input type="password" name="password">
    </div>

    <div>
        <label for="remember">
            <input type="checkbox" name="remember">remember me
        </label>
    </div>

    <button type="submit">Register</button>

    <p>Not yet a member? Sign up <a href="register.php">here</a></p>
    <p><a href="forgot_password.php">forgot password?</a></p>

</form>

</body>
</html>
