<?php

namespace App;

use App\Helpers\Models\DatabaseObject;
use App\Helpers\Token\Token;


/**
 * Project: startup
 * File: Remember.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 12/10/2019 1:31 PM
 *
 * Contact: princetunes@gmail.com
 *
 */

class Remember extends DatabaseObject
{
    static protected $table_name = "remembered_users";
    static protected $db_columns = [
        'token_hash',
        'user_id',
        'expires_at',
    ];

    public $token_hash;
    public $user_id;
    public $expires_at;

    public function save_login () {
        $token = new Token();
        $this->cookie_token = $token->getValue();

        $this->token_hash = $token->getHash();
        $this->user_id;
        $this->expires_at = date('Y-m-d H:i:s', time() + (60 * 60 * 24) * 30); // seconds for 30 days;

        return $this->create()->now()->count();
    }

    public function findToken (string $cookie) {
        $token = new Token($cookie);
        if ($token->getHash()) {
            return $this->retrieve()->where(['token_hash', '=', $token->getHash()])->limit()->now()->results();
        }
        return false;
    }

    public function deleteRemember (string $token) {
        return $this->delete()->where(['token_hash', '=', $token])->now()->count();
    }

}