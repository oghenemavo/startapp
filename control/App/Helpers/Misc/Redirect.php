<?php
/**
 * Project: startup
 * File: Redirect.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 12/10/2019 2:59 PM
 *
 * Contact: princetunes@gmail.com
 *
 */

namespace App\Helpers\Misc;


class Redirect
{

    /**
     * @param null $location
     */
    public static function to($location = null) {
        if($location) {
            if(is_numeric($location)) {
                switch($location) {
                    case 403:
                        header('HTTP/1.0 403 Access forbidden!');
                        include '../views/errors/404.html';
                        exit();
                        break;
                    case 404:
                        header('HTTP/1.0 404 Not Found');
                        include '../views/errors/404.html';
                        exit();
                        break;
                }
            }
            header('Location: ' . $location, true, 303);
            exit();
        }
    }

}