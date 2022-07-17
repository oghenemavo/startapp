<?php
/**
 * Project: startup
 * File: logout.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 11/10/2019 8:48 PM
 *
 * Contact: princetunes@gmail.com
 *
 */


use App\Helpers\Auth;
use App\Helpers\Misc\Redirect;

require_once 'control/core/init.php';

Auth::getInstance()->logout();

Redirect::to('/startup');