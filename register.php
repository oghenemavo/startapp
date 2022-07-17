<?php
/**
 * Project: startup
 * File: register.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 29/09/2019 3:22 PM
 *
 * Contact: princetunes@gmail.com
 *
 */


use App\Helpers\Auth;
use App\Helpers\Misc\Session;
use App\Helpers\Validator\Validator;
use App\User;

require_once 'control/core/init.php';

Auth::getInstance()->require_guest();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = array_map('trim', $_POST);

    $scrutinize->addValidator('unique', new Validator($container['db_connection']));

    $validation = $scrutinize->make($form, [
            'fullname'  => 'required|min:3|regex:/^[a-zA-Z]+(([\',. -][a-zA-Z ])?[a-zA-Z]*)*$/',
            'username'  => 'required||min:3|max:15|regex:/^[a-z0-9_]{3,15}$/i|unique:users,username,random_username',
            'email'     => 'required|email|unique:users,email,exception@mail.com',
            'password'  => 'required|min:5',
            'terms'  => 'accepted',
    ]);

    $validation->setAlias('fullname', 'Full Name');

    $validation->validate();

    $user = new User($form);

    if ($validation->fails()) {
        // handling errors
        $errors = $validation->errors();
    } else {
        // validation passes
        if ($user->create_account()) {
            Session::flash('success', 'Registration Successful');
        }
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
    <title>Sign up</title>
</head>
<body>

    <h1>Sign up</h1>

    <form method="post">
        <div>
            <label for="fullname">full name</label>
            <input type="text" name="fullname" value="<?php echo $form['fullname'] ?? ''; ?>" >
            <div class="errors"> <?php echo isset($errors) ? $errors->first('fullname') : ''; ?> </div>
        </div>

        <div>
            <label for="username">username</label>
            <input type="text" name="username" value="<?php echo $form['username'] ?? ''; ?>">
            <div class="errors"> <?php echo isset($errors) ?  $errors->first('username') : ''; ?> </div>
        </div>

        <div>
            <label for="email">email</label>
            <input type="email" name="email" value="<?php echo $form['email'] ?? ''; ?>">
            <div class="errors"> <?php echo isset($errors) ?  $errors->first('email') : ''; ?> </div>
        </div>

        <div>
            <label for="password">password</label>
            <input type="password" name="password">
            <div class="errors"> <?php echo isset($errors) ?  $errors->first('password') : ''; ?> </div>
        </div>

        <div>
            <label for="terms">terms</label>
            <input type="checkbox" name="terms" value="on">
            <div class="errors"> <?php echo isset($errors) ?  $errors->first('terms') : ''; ?> </div>
        </div>

        <button type="submit">Register</button>
    </form>

<p>Already a member? log in <a href="login.php">here</a></p>

</body>
</html>
