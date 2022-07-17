<?php

use App\Helpers\Auth;
use App\Helpers\Misc\Pagination;
use App\Helpers\Misc\Session;
use App\User;

require_once '../control/core/init.php';

Auth::getInstance()->require_admin();

Auth::getInstance()->require_role('Super Admin');

$user = new User();

$total_count = $user->count_all_users();
//echo '<pre>' . var_export($total_count, true) . '</pre>';

$current_page = $_GET['page'] ?? 1;
$per_page = $limit = 8;

$pagination = new Pagination($current_page, $per_page, $total_count);

$offset = $pagination->offset();

$all_users = $user->get_users($limit, $offset);


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

<h1>Total Users</h1>

<?php

if (Session::exists('success')) {
    echo Session::flash('success');
}

?>

<table>
    <thead>
    <tr>
        <th>s/n</th>
        <th>name</th>
        <th>email</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($all_users as $member): ?>
    <tr>
        <td><?php echo $member->id; ?></td>
        <td><a href="show_user.php?id=<?php echo $member->id; ?>"><?php echo $member->full_name; ?></a></td>
        <td><?php echo $member->email; ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php

echo $pagination->previous_link();
echo $pagination->next_link();

?>


</body>
</html>

