<?php

use App\Helpers\Auth;
use App\User;

require_once '../control/core/init.php';

Auth::getInstance()->require_guest();

$activated = false;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['auth'])) {
    if (User::activate_account($_GET['auth'])) {
        $activated = true;
    }
}

?>

<?php if ($activated): ?>
<p>Account has been successfully updated! log in <a href="../login.php">here</a> </p>
<?php else: ?>
<p>Invalid activation</p>
<?php endif; ?>