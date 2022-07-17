<?php
/**
 * Project: startup
 * File: Session.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 12/10/2019 2:59 PM
 *
 * Contact: princetunes@gmail.com
 *
 */

namespace App\Helpers\Misc;


class Session
{

    /**
     * Check if a session cookie exist
     *
     * @param $name
     * @return bool
     */
    public static function exists($name) {
        return (isset($_SESSION[$name])) ? true : false;
    }

    /**
     * Set a value to a session cookie
     *
     * @param $name     Session name
     * @param $value    Session value
     * @return mixed
     */
    public static function put($name, $value) {
        return $_SESSION[$name] = $value;
    }

    /**
     * Get a session value from the name
     *
     * @param $name     Session name
     * @return mixed
     */
    public static function get($name) {
        return $_SESSION[$name];
    }

    /**
     * Remove a session
     * @param $name
     */
    public static function delete($name) {
        if(self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * @param $name
     * @param string $string
     * @return mixed
     */
    public static function flash($name, $string = '') {
        if(self::exists($name)) {
            $session = self::get($name);
            self::delete($name);
            return $session;
        } else {
            self::put($name, $string);
        }
    }

}