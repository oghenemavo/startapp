<?php

use App\Helpers\Auth;
use App\Helpers\Misc\Redirect;
use App\Helpers\Misc\Session;
use App\User;

require_once '../control/core/init.php';

Auth::getInstance()->require_guest();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['reset'])) {
    $user = User::findPasswordResetToken($_GET['reset']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = User::findPasswordResetToken(base64_decode($_POST['password_key']));
    if ($user) {
        $form = array_map('trim', $_POST);

        $validation = $scrutinize->make($form, [
            'password'  => 'required|min:5',
            'password_confirm'  => 'required|same:password',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            // handling errors
            $errors = $validation->errors();
        } else {
            // validation passes
            if ($user->reset_password($form['password'])) {
                Session::flash('login', 'password changed Successfully, log in now!');
                Redirect::to('/login.php');
            }
        }

    }
}

?>

<h1>Reset Password</h1>

<?php if ($user): ?>
<form method="post">
    <input type="hidden" name="password_key" value="<?php echo base64_encode($_GET['reset']); ?>">

    <div>
        <label for="password">New password</label>
        <input type="password" name="password">
        <div class="errors"> <?php echo isset($errors) ?  $errors->first('password') : ''; ?> </div>
    </div>

    <div>
        <label for="password">Retype new password</label>
        <input type="password" name="password_confirm">
        <div class="errors"> <?php echo isset($errors) ?  $errors->first('password_confirm') : ''; ?> </div>
    </div>

    <button type="submit">Reset</button>
</form>
<?php else: ?>
<p>Reset key not found or expired. please try resetting your password again by clicking <a href="../forgot_password.php">here</a> </p>
<?php endif; ?>
