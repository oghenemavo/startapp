<?php

use App\Helpers\Auth;

require_once 'control/core/init.php';

Auth::getInstance()->require_admin();