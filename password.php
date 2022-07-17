<?php

use App\Helpers\Auth;
use App\Helpers\Misc\Redirect;
use App\Helpers\Misc\Session;
use App\User;

require_once 'control/core/init.php';

Auth::getInstance()->require_login();

$user = Auth::getInstance()->get_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = array_map('trim', $_POST);

    $validation = $scrutinize->make($form, [
        'old_password'  => [
                'required',
            function ($value) use ($user) {
                if (! password_verify($value, $user->password)) {
                    return ":attribute does not match current password.";
                }
            }
        ],
        'new_password'  => 'required|min:5',
        'password_confirm'  => 'required|same:new_password',
    ]);

    $validation->validate();

    if ($validation->fails()) {
        // handling errors
        $errors = $validation->errors();
    } else {
        // validation passes
        $form['password'] = $form['new_password'];
        unset($form['new_password']);

        $u = new User($form);

        if ($u->update_profile($user->id)) {
            Session::flash('success', 'Password updated Successfully');
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
        <label for="password">Old password</label>
        <input type="password" name="old_password">
        <div class="errors"> <?php echo isset($errors) ?  $errors->first('old_password') : ''; ?> </div>
    </div>

    <div>
        <label for="password">New password</label>
        <input type="password" name="new_password">
        <div class="errors"> <?php echo isset($errors) ?  $errors->first('new_password') : ''; ?> </div>
    </div>

    <div>
        <label for="password">Retype new password</label>
        <input type="password" name="password_confirm">
        <div class="errors"> <?php echo isset($errors) ?  $errors->first('password_confirm') : ''; ?> </div>

    <button type="submit">save</button>
</form>