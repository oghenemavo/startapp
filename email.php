<?php

use App\Helpers\Auth;
use App\Helpers\Misc\Redirect;
use App\Helpers\Misc\Session;
use App\Helpers\Validator\Validator;
use App\User;

require_once 'control/core/init.php';

Auth::getInstance()->require_login();

$user = Auth::getInstance()->get_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = array_map('trim', $_POST);
    $u = new User($form);

    $scrutinize->addValidator('unique', new Validator($container['db_connection']));

    $validation = $scrutinize->make($form, [
        'email'     => "required|email|unique:users,email,{$user->email}",
    ]);

    $validation->validate();

    if ($validation->fails()) {
        // handling errors
        $errors = $validation->errors();
    } else {
        // validation passes
        if ($u->update_profile($user->id)) {
            Session::flash('success', 'Email updated Successfully');
        } else {
            Session::flash('success', 'No changes made!');
        }
        Redirect::to($_SERVER['PHP_SELF']);
    }

}

?>

<h1>Edit</h1>

<?php
if (Session::exists('success')) {
    echo Session::flash('success');
}
?>

<form method="post">
    <div>
        <label for="email">email</label>
        <input type="email" name="email" value="<?php echo $user->email ?? ''; ?>" >
        <div class="errors"> <?php echo isset($errors) ? $errors->first('email') : ''; ?> </div>
    </div>

    <button type="submit">save</button>
</form>