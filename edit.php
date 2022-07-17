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
    $u = new User($form);

    $validation = $scrutinize->make($form, [
        'fullname'  => 'required|min:3|regex:/^[a-zA-Z]+(([\',. -][a-zA-Z ])?[a-zA-Z]*)*$/',
    ]);

    $validation->setAlias('fullname', 'Full Name');

    $validation->validate();

    if ($validation->fails()) {
        // handling errors
        $errors = $validation->errors();
    } else {
        // validation passes
        if ($u->update_profile($user->id)) {
            Session::flash('success', 'Profile updated Successfully');
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
        <label for="fullname">full name</label>
        <input type="text" name="fullname" value="<?php echo $user->full_name ?? ''; ?>" >
        <div class="errors"> <?php echo isset($errors) ? $errors->first('fullname') : ''; ?> </div>
    </div>

    <button type="submit">Register</button>
</form>