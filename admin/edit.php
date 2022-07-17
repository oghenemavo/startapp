<?php

use App\Helpers\Auth;
use App\Helpers\Misc\Redirect;
use App\Helpers\Misc\Session;
use App\Helpers\Validator\Validator;
use App\User;

require_once '../control/core/init.php';

$auth = Auth::getInstance();

$auth->require_admin();
$auth->require_role('Super Admin');

$user_obj = User::getById_or404((int) $_GET['id']);
$user = $user_obj->results();

//echo '<pre>' . var_export($user, true) . '</pre>';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = array_map('trim', $_POST);

    $scrutinize->addValidator('unique', new Validator($container['db_connection']));

    $validation = $scrutinize->make($form, [
        'full_name'  => 'required|min:3|regex:/^[a-zA-Z]+(([\',. -][a-zA-Z ])?[a-zA-Z]*)*$/',
        'username'  => "required||min:3|max:15|regex:/^[a-z0-9_]{3,15}$/i|unique:users,username,{$user->username}",
        'email'     => "required|email|unique:users,email,{$user->email}",
        'password'  => 'min:5',
    ]);

    $validation->setAlias('full_name', 'Full Name');

    $validation->validate();

    $user_obj->merge_attributes($form);

    if ($validation->fails()) {
        // handling errors
        $errors = $validation->errors();
    } else {
        // validation passes
        if ($user_obj->save()) {
            Session::flash('success', "{$user->username}'s profile has been updated!");
            Redirect::to($_SERVER['REQUEST_URI']);
        } else {
            Session::flash('success', "No changes made!");
            Redirect::to($_SERVER['REQUEST_URI']);
        }
    }

}
//echo '<pre>' . var_export($errors, true) . '</pre>';

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

<h1>Edit User</h1>

<?php

if (Session::exists('success')) {
    echo Session::flash('success');
}

require_once 'user_form.php';

?>

</body>
</html>

