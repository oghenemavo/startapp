<?php

use App\Helpers\Auth;
use App\Helpers\Misc\Session;
use App\User;

require_once '../control/core/init.php';

Auth::getInstance()->require_admin();
Auth::getInstance()->require_role('Super Admin');

$user = User::getById_or404((int) $_GET['id'])->results();

//echo '<pre>' . var_export($all_users, true) . '</pre>';

?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Profile</title>
</head>
<body>

<h1>Show User</h1>

<?php

if (Session::exists('success')) {
    echo Session::flash('success');
}

?>

<dl>
    <dt>Name</dt>
    <dd><?php echo $user->full_name; ?></dd>

    <dt>Email</dt>
    <dd><?php echo $user->email; ?></dd>

    <dt>Username</dt>
    <dd><?php echo $user->username; ?></dd>

    <dt>Date Registered</dt>
    <dd><?php echo date('d-m-Y', strtotime($user->created_at)); ?></dd>

    <dt>Active</dt>
    <dd><?php echo $user->is_active ? '&#10004;' : '&#10008;'; ?></dd>
    <dt>Administrator</dt>
    <dd><?php echo $user->is_admin ? '&#10004;' : '&#10008;'; ?></dd>
</dl>

<ul>
    <li><a href="edit.php?id=<?php echo $user->id; ?>">Edit</a></li>
    <li>
        <?php if ($user->id == Auth::getInstance()->get_user()->id): ?>
            Delete
        <?php else: ?>
<!--        create a modal and add a form to it-->
            <a href="/admin/users/delete.php?id=<?php echo $user->id; ?>">Delete</a>
        <?php endif; ?>
    </li>
</ul>

</body>
</html>

