<?php
/**
 * Project: startup
 * File: User.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 30/09/2019 10:50 AM
 *
 * Contact: princetunes@gmail.com
 *
 */

namespace App;

use App\Helpers\Misc\Redirect;
use DateTime;
use DateInterval;
use Exception;
use App\Helpers\Mail;
use App\Helpers\Misc\Session;
use App\Helpers\Misc\View;
use App\Helpers\Models\DatabaseObject;
use App\Helpers\Token\Token;


class User extends DatabaseObject {

    static protected $table_name = "users";

    static protected $db_columns = [
        'id',
        'full_name',
        'username',
        'email',
        'password',
        'password_reset_token',
        'password_reset_expiry',
        'activation_token',
        'is_active',
        'is_admin',
        'is_group',
        'created_at',
    ];

    public $id;
    public $full_name;
    public $username;
    public $email;
    protected $password;
    public $password_reset_token;
    public $password_reset_expiry;
    public $activation_token;
    public $is_active;
    public $is_admin;
    public $is_group;
    public $created_at;

    public function __construct (array $data = []) {
        $this->full_name = $data['fullname'] ?? '';
        $this->username = $data['username'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password_init = $data['password'] ?? '';
    }

    /**
     * Sign up a new user
     *
     * @return bool
     * @throws Exception
     */
    public function create_account () {
        $token = new Token();
        $this->email_token = $token->getValue();
        $this->activation_token = $token->getHash();
        $this->set_hashed_password($this->password_init);

        $date = new DateTime();
        $this->created_at = $date->format('Y-m-d H:i:s');
        if ($this->create()->now()->count()) {
            $this->_sendActivationEmail();
            return true;
        }
        return false;
    }

    /**
     * Start the password reset process by generating a unique token and expiry and saving them in the user model
     *
     * @return int
     * @throws Exception
     */
    public function startPasswordReset () {
        $token = new Token();
        $this->email_token = $token->getValue();
        $this->password_reset_token = $token->getHash();

        $date = new DateTime();
        $interval = new DateInterval('PT2H'); // 2 hours added to current time for expiration
        $date->add($interval);
        $this->password_reset_expiry = $date->format('Y-m-d H:i:s');
        return $this->update()->where(['id', '=', $this->results()->id])->now()->count();
    }

    /**
     * Reset the password
     * @param string $password      Password
     * @return int
     * @throws Exception
     */
    public function reset_password (string $password) {
        $this->set_hashed_password($password);
        return $this->update(['password_reset_token', 'password_reset_expiry'])->where(['id', '=', $this->results()->id])->now()->count();
    }

    /**
     * Update a user profile
     *
     * @param int $user_id
     * @return int
     * @throws Exception
     */
    public function update_profile (int $user_id) {
        if (isset($this->password_init)) {
            $this->set_hashed_password($this->password_init);
        }
        return $this->update()->where(['id', '=', $user_id])->now()->count();
    }

    /**
     * Find the user with the specified Email
     *
     * @param string $email             Email
     * @return DatabaseObject|bool      User object if found, null otherwise
     * @throws Exception
     */
    static public function findByEmail (string $email) {
        $user = (new self)->retrieve()->where(['email', '=', $email])->limit()->now();

        if ($user->count()) {
            return $user;
        }
    }

    /**
     * Find the user with the specified ID
     *
     * @param int $id       ID
     * @return mixed        User object if found, null otherwise
     * @throws Exception
     */
    static public function findById (int $id) {
        $user = (new self)->retrieve()->where(['id', '=', $id])->limit()->now();

        if ($user->count()) {
            return $user;
        }
    }

    /**
     * Authenticate a user by Identifier and password
     *
     * @param string $identifier        Username|Email
     * @param string $password
     * @return bool|mixed               User object if authenticated correctly, false otherwise
     * @throws Exception
     */
    static public function authenticate (string $identifier, string $password) {
        $user = static::findByIdentifier($identifier);
        if ($user !== null) {
            if (password_verify($password, $user->password)) {
                if ($user->is_active) {
                    return $user;
                } else {
                    Session::flash('login', 'Activate your account, check your email for previously sent activation mail');
                }
            }
        }
        return false;
    }

    /**
     * Find the user with the specified email or username
     *
     * @param string $identifier    Username|Email
     * @return mixed                User object if found, null otherwise
     * @throws Exception
     */
    static public function findByIdentifier (string $identifier) {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $column = 'email';
        } else {
            $column = 'username';
        }
        $user =  (new self)->retrieve()->where([$column, '=', $identifier])->limit()->now();
        if ($user->count()) {
            return $user->results();
        }
    }

    /**
     * Activate the user account, nullifying the activation token and setting the is_active flag
     *
     * @param $email_token      Activation token
     * @return bool|int
     * @throws Exception
     */
    static public function activate_account ($email_token) {
        $self = new self;

        $token = new Token($email_token);
        $token_hash = $token->getHash();
        $user = $self->retrieve()->where(['activation_token', '=', $token_hash])->limit()->now();
        if ($user->count()) {
            $self->is_active = 1;
            return $self->update(['activation_token'])->where(['id', '=', $user->results()->id])->now()->count();
        }
        return false;
    }

    /**
     * Find the user for password reset, by the specified token and check the token hasn't expired
     *
     * @param $email_token
     * @return DatabaseObject|bool      User object if found and the token hasn't expired, false otherwise
     * @throws Exception
     */
    static public function findPasswordResetToken ($email_token) {
        $self = new self;

        $token = new Token($email_token);
        $token_hash = $token->getHash();
        $user = $self->retrieve()->where(['password_reset_token', '=', $token_hash])->limit()->now();
        if ($user->count()) {
            $expiry = DateTime::createFromFormat('Y-m-d H:i:s', $user->results()->password_reset_expiry);
            if ($expiry->getTimestamp() > time()) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Encrypt password
     *
     * @param string $password
     * @return bool|string
     */
    protected function set_hashed_password (string $password) {
        return $this->password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    private function _sendActivationEmail () {
        $url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/auth/activate_account.php?auth=' . $this->email_token;

        $to = ['address' => $this->email, 'name' => $this->username];
        $subject = 'Activate account';
        $text = View::getTemplate('text/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('mail/activation_email.php', ['url' => $url]);

        $body = ['html' => $html, 'alt' => $text];

        Mail::send($to, $subject, $body);
    }

    static public function getById_or404($id) {
        $user = static::findByID($id);

        if ($user !== null) {
            return $user;
        }
        Redirect::to(404);
    }

    public function save() {
        if (!empty($this->password)) {
            $this->set_hashed_password($this->password);
        } else {
            $this->password = null;
        }

        $this->is_active = isset($this->is_active) && ($this->is_active == '1') ? '1' : '0';
        $this->is_admin = isset($this->is_admin) && ($this->is_admin == '1') ? '1' : '0';
        $this->is_group = $this->is_group ?? null;

        if(isset($this->results()->id)) {
            return $this->update()->where(['id', '=', $this->results()->id])->now()->count();
        } else {
            return $this->create()->now();
        }
    }

    public function delete_user() {
        return $this->delete()->where(['id', '=', $this->results()->id])->now();
    }

    public function count_all_users() {
        return $this->count_all()->results();
    }

    public function get_users($limit = 5, $offset = 0) {
        return $this->retrieve()->limit($limit)->offset($offset)->now()->results();
    }

}