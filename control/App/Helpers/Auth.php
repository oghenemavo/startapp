<?php
/**
 * Project: startup
 * File: Auth.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 12/10/2019 2:19 PM
 *
 * Contact: princetunes@gmail.com
 *
 */

namespace App\Helpers;

use Exception;
use App\Helpers\Misc\Redirect;
use App\Helpers\Misc\Session;
use App\Helpers\Misc\View;
use App\Remember;
use App\User;

class Auth
{

    private $_user = false;

    static protected $_instance;

    private function __construct() {}  // disallow creating a new object of the class with new Auth()

    private function __clone() {}  // disallow cloning the class

    /**
     * Get the singleton instance
     *
     * @return Auth
     */
    static public function getInstance() {
        if (null === static::$_instance) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * Log the user in from the remember me cookie
     *
     * @return mixed|bool       User object if logged in correctly from the cookie, or false otherwise
     * @throws Exception
     */
    private function _loginFromCookie () {
        $cookie = $_COOKIE['uAuth'] ?? false;
        if ($cookie) {
            $remember = new Remember();
            $remembered = $remember->findToken($cookie);
            if ($remembered) {
                $user = User::findById($remembered->user_id);
                if ($user !== null) {
                    $this->_user = $user->results();
                    Session::put('user_id', $this->_user->id);
                    session_regenerate_id(true);
                }
            }
        }
        return $this->_user;
    }

    /**
     * Login a User
     *
     * @param $identifier       Email Address|Username
     * @param $password         Password
     * @param $remember_me      Remember the log in flag
     * @return bool             true if the new user record was saved successfully, false otherwise
     * @throws Exception
     */
    public function login ($identifier, $password, $remember_me) {
        $user = User::authenticate($identifier, $password);
        if ($user !== false) {
            $this->_user = $user;
            Session::put('user_id', $this->_user->id);
            session_regenerate_id(true);
            if ($remember_me) {
                $remember = new Remember();
                $remember->user_id = $this->_user->id;
                if ($remember->save_login()) {
                    setcookie('uAuth', $remember->cookie_token, strtotime($remember->expires_at), '/');
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Get the current logged in user
     *
     * @return bool|mixed User object if logged in, false otherwise
     * @throws Exception
     */
    public function get_user() {
        if (Session::exists('user_id')) {
            $this->_user = User::findById(Session::get('user_id'))->results();
        } else {
            $this->_user = $this->_loginFromCookie();
        }
        return $this->_user;
    }

    /**
     * Boolean indicator of whether the user is logged in or not
     *
     * @return bool
     * @throws Exception
     */
    public function isLoggedIn () {
        return $this->get_user() !== false;
    }

    /**
     * Logout a user
     *
     * @return void
     */
    public function logout () {
        // Unset all of the session variables
        $_SESSION = [];

        // Delete the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Finally destroy the session
        session_destroy();

        $cookie = $_COOKIE['uAuth'] ?? false;
        if ($cookie) {
            $remember = new Remember();
            $remembered = $remember->findToken($cookie);
            if ($remembered) {
                if ($remember->deleteRemember($remembered->token_hash)) {
                    setcookie('uAuth', null, -1, '/');
                    unset($_COOKIE['uAuth']);
                }
            }
        }
    }

    /**
     * Redirect to the home page if a user is logged in.
     *
     * @throws Exception
     */
    public function require_guest () {
        if ($this->isLoggedIn()) {
            Redirect::to('/');
        }
    }

    /**
     * Send the user password reset email
     *
     * @param $email Email
     * @return bool
     * @throws Exception
     */
    public function sendPasswordReset ($email) {
        $user = User::findByEmail($email);
        if ($user !== null) {
            if ($user->startPasswordReset()) {
                $url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/auth/reset_password.php?reset=' . $user->email_token;

                $to = ['address' => $user->results()->email, 'name' => $user->results()->username];
                $subject = 'Start Password Reset';
                $text = View::getTemplate('text/password_reset.txt', ['url' => $url]);
                $html = View::getTemplate('mail/password_reset.html', ['url' => $url]);

                $body = ['html' => $html, 'alt' => $text];

                Mail::send($to, $subject, $body);
                return true;
            }
        }
        return false;
    }

    /**
     * Redirect to the login page if no user is logged in.
     *
     * @throws Exception
     */
    public function require_login () {
        if (!$this->isLoggedIn()) {

            $url = $_SERVER['REQUEST_URI'];
            if ( ! empty($url)) {
                $_SESSION['return_to'] = $url; // Save the requested page to return to after logging in
            }
            Redirect::to('/login.php');
        }
    }

    /**
     * Boolean indicator of whether the user is logged in and is an administrator
     *
     * @return bool
     * @throws Exception
     */
    public function is_admin () {
        return $this->isLoggedIn() && $this->_user->is_admin;
    }

    /**
     * Show a forbidden message if the current logged in user is not an administrator.
     *
     * @return void
     * @throws Exception
     */
    public function require_admin () {
        if (! $this->is_admin()) {
            Redirect::to(403);
        }
    }

    public function require_permission ($permission) {
        if (! $this->has_permission($permission)) {
            Redirect::to('/');
        }
    }

    public function require_role ($role) {
        if (! $this->has_role($role)) {
            Redirect::to('/');
        }
    }

    public function has_permission($permission) {
        return (bool) $this->_user->$permission;
    }

    public function has_role($role) {
        if (is_array($role) && count($role) > 0) {
            for ($i=0; $i<count($role); $i++) {
                if ($this->_user->is_group == ucwords($role[$i])) {
                    return true;
                }
            }
            return false;
        } else {
            return (bool) $this->_user->is_group == ucwords($role);
        }
    }

}