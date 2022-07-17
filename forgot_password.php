<?php

use App\Helpers\Auth;
use App\Helpers\Misc\Redirect;
use App\Helpers\Misc\Session;

require_once 'control/core/init.php';

Auth::getInstance()->require_guest();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = array_map('trim', $_POST);

    $validation = $scrutinize->make($form, [
            'user_identifier' => 'required|email'
    ]);

    $validation->validate();

    if ($validation->fails()) {
        // handling errors
        $errors = $validation->errors();
    } else {
        // validation passes
        if (Auth::getInstance()->sendPasswordReset($form['user_identifier'])) {
            Session::flash('password', 'password reset instructions has been sent to your email');
            Redirect::to($_SERVER['PHP_SELF']);
        } else {
            Session::flash('password', 'Invalid Email entered');
        }
    }


}

?>

<h1>Forgot password</h1>

<?php

if (Session::exists('password')) {
    echo Session::flash('password');
}

?>

<form method="post">
    <div>
        <label for="username">Email</label>
        <input type="text" name="user_identifier" value="<?php echo $form['user_identifier'] ?? ''; ?>" placeholder="email">
        <div class="errors"> <?php echo isset($errors) ?  $errors->first('user_identifier') : ''; ?> </div>
    </div>

    <button type="submit">Send password reset instruction</button>
</form>
