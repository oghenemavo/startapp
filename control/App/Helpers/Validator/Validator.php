<?php
/**
 * Project: startup
 * File: Validator.php
 *
 * Initial version by: @oghenemavo
 * Initial version created on: 12/10/2019 3:25 PM
 *
 * Contact: princetunes@gmail.com
 *
 */

namespace App\Helpers\Validator;


use PDO;
use Rakit\Validation\Rule;

class Validator extends Rule
{

    protected $message = ":attribute :value has been used";

    protected $fillableParams = ['table', 'column', 'except'];

    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function check($value): bool
    {
        // TODO: Implement check() method.
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);

        // getting parameters
        $column = $this->parameter('column');
        $table = $this->parameter('table');
        $except = $this->parameter('except');

        if ($except AND $except == $value) {
            return true;
        }

        // do query
        $stmt = $this->pdo->prepare("select count(*) as count from `{$table}` where `{$column}` = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // true for valid, false for invalid
        return intval($data['count']) === 0;
    }

}