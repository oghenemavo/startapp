<?php

use App\Helpers\Auth;
use App\Helpers\Misc\Redirect;
use App\Helpers\Misc\Session;
use App\User;

require_once '../control/core/init.php';

Auth::getInstance()->require_admin();
Auth::getInstance()->require_role('Super Admin');

$user = User::getById_or404((int) $_GET['id']);

// Process the submitted form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user->delete_user();
    Session::flash('success', 'Delete successful');
    // Redirect to index page
    Redirect::to('/admin');
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>

<h1>Delete User</h1>

<form method="post">

    <p>Are you sure?</p>

    <input type="submit" value="Delete" />
    <a href="/admin/users/show.php?id=<?php echo $user->id; ?>">Cancel</a>
</form>

</body>
</html>
