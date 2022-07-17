<?php

namespace App\Helpers\Models;

use PDO;
use PDOException;
use Exception;

class DatabaseObject {

    static protected $database;
    static protected $table_name = "";
    static protected $db_columns = [];

    private $_sql,
        $_stmt,
        $_attributes = [],
        $_error = false,
        $_results,
        $_count = 0;

    static public function set_database($database) {
        return self::$database = $database;
    }

    protected function query ($sql, $attributes = []) {
        try {
            if (self::$database) {
                if (isset($attributes) && is_array($attributes) && count($attributes)) {
                    if ($this->_stmt = self::$database->prepare($sql)) {
                        for ($i=0; $i < count($attributes); $i++) {
                            $this->_stmt->bindValue(array_keys($attributes)[$i], array_values($attributes)[$i]);
                        }

                        if ($this->_stmt->execute()) {
                            $this->_count = $this->_stmt->rowCount();
                            if (stripos($this->_sql, 'INSERT') !== false) {
                                $column = static::$db_columns[0];
                                $this->$column = self::$database->lastInsertId();
                            }
                            if (strpos($this->_sql, 'SELECT') === 0) {
                                $this->_stmt->setFetchMode(PDO::FETCH_OBJ);
                                $this->_results = trim(substr(trim($this->_sql), -2)) === '1' ? $this->_stmt->fetch() : $this->_stmt->fetchAll();
                            }
                        } else {
                            $this->_error = true;
                        }
                    }
                } else {
                    if ($this->_stmt = self::$database->query($sql)) {
                        $this->_stmt->setFetchMode(PDO::FETCH_OBJ);
                        $this->_results = trim(substr(trim($this->_sql), -2)) === '1' ? $this->_stmt->fetch() : $this->_stmt->fetchAll();
                        $this->_count = $this->_stmt->rowCount();
                    } else {
                        return $this->_error = true;
                    }
                }
                return $this;
            }
            throw new PDOException('Database connection Failed...');
        } catch (PDOException $ex) {
            throw new PDOException($ex->getMessage());
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    protected function retrieve ($columns = []) {
        $sql = "SELECT * FROM " . static::$table_name . " ";
        if (count($columns)) {
            $sql = "SELECT " . implode(', ', $columns) . " FROM " . static::$table_name . " ";
        }
        $this->_sql = $sql;
        return $this;
    }

    protected function count_all () {
        try {
            $this->_sql = "SELECT COUNT(*) FROM " . static::$table_name . " ";
            if ($this->_stmt = self::$database->query($this->_sql)) {
                $this->_stmt->setFetchMode(PDO::FETCH_ASSOC);
                $total_arr = $this->_stmt->fetch();
                $this->_results = array_shift($total_arr);
                $this->_count = $this->_stmt->rowCount();
            } else {
                return $this->_error = true;
            }
        } catch (PDOException $ex) {

        }
        return $this;
    }

    protected function alias (string $name) {
        $refer = "AS $name ";
        $this->_sql .= $refer;
        return $this;
    }

    protected function join (string $table_name) {
        $join = "JOIN $table_name ";
        $this->_sql .= $join;
        return $this;
    }

    protected function leftJoin (string $table_name) {
        $join = "LEFT JOIN $table_name ";
        $this->_sql .= $join;
        return $this;
    }

    protected function rightJoin (string $table_name) {
        $join = "RIGHT JOIN $table_name ";
        $this->_sql .= $join;
        return $this;
    }

    protected function innerJoin (string $table_name) {
        $join = "INNER JOIN $table_name ";
        $this->_sql .= $join;
        return $this;
    }

    protected function fullJoin (string $table_name) {
        $join = "FULL OUTER JOIN $table_name ";
        $this->_sql .= $join;
        return $this;
    }

    protected function on (array $relationship) {
        $on = "ON $relationship[0] = $relationship[1] ";
        $this->_sql .= $on;
        return $this;
    }

    protected function groupBy (array $groups) {
        $list = "GROUP BY " . implode(', ', $groups) . " ";
        $this->_sql .= $list;
        return $this;
    }

    protected function where ($where = []) {
        // ['col1', '=', 'value1']
        $operators = ['<', '>', '=', '<=', '>='];

        if (!preg_match('(SELECT|UPDATE|DELETE)', $this->_sql, $matches)) {
            throw new Exception("Error, sql needs SELECT, UPDATE or DELETE keyword");
        }

        if (stripos($this->_sql, 'WHERE') !== false) {
            throw new Exception("Error, sql cannot have more than one WHERE keyword");
        }

        if (isset($where) && is_array($where) && count($where) == 3) {
            $column = (string) $where[0];
//            $column_ph = ':' . $column;
            $column_ph = ':' . str_replace('.', '', $column);
            $operand = in_array($where[1], $operators) ? $where[1] : '=';
            $value = (string) $where[2];

            $attribute_pairs = [];
            $attribute_pairs[$column_ph] = $value;

            $concat = "WHERE {$column} {$operand} {$column_ph} ";

            $this->_attributes = count($this->_attributes) ? array_merge($this->_attributes, $attribute_pairs) : $attribute_pairs;

            $this->_sql .= $concat;
            return $this;
        } else {
            throw new Exception("Error, array values must be equal to three");
        }
    }

    protected function orwhere ($column_pairs = []) {
        if (stripos($this->_sql, 'WHERE') === false) {
            throw new Exception("Error, sql needs a WHERE clause");
        }

        $operators = ['<', '>', '=', '<=', '>='];

        if (count($column_pairs) == 3) {
            $column = (string) $column_pairs[0];
//            $column_ph = ':' . $column;
            $column_ph = ':' . str_replace('.', '', $column);
            $operand = in_array($column_pairs[1], $operators) ? $column_pairs[1] : '=';
            $value = (string) $column_pairs[2];

            $attribute_pairs = [];
            $attribute_pairs[$column_ph] = $value;

            $concat = "OR {$column} {$operand} {$column_ph} ";

            $this->_sql .= $concat;
            $this->_attributes = count($this->_attributes) ? array_merge($this->_attributes, $attribute_pairs) : $attribute_pairs;

            return $this;
        } else {
            throw new Exception("Error, array values must be equal to three");
        }

    }

    protected function andwhere ($column_pairs = []) {

        if (stripos($this->_sql, 'WHERE') === false) {
            throw new Exception("Error, sql needs where clause");
        }

        if (stripos($this->_sql, 'AND') !== false) {
            throw new Exception("Error, sql cannot have more than one AND clause");
        }

        $operators = ['<', '>', '=', '<=', '>='];

        if (count($column_pairs) == 3) {
            $column = (string) $column_pairs[0];
//            $column_ph = ':' . $column;
            $column_ph = ':' . str_replace('.', '', $column);
            $operand = in_array($column_pairs[1], $operators) ? $column_pairs[1] : '=';
            $value = (string) $column_pairs[2];

            $attribute_pairs = [];
            $attribute_pairs[$column_ph] = $value;

            $concat = "AND {$column} {$operand} {$column_ph} ";

            $this->_sql .= $concat;
            $this->_attributes = count($this->_attributes) ? array_merge($this->_attributes, $attribute_pairs) : $attribute_pairs;

            return $this;
        } else {
            throw new Exception("Error, array values must be equal to three");
        }
    }

    protected function orderby ($columns = [], $order = []) {
        $orderBy = ['ASC', 'DESC'];

        if (stripos($this->_sql, 'SELECT') === false) {
            throw new Exception("Error, sql needs SELECT keyword");
        }
        if (stripos($this->_sql, 'LIMIT') !== false) {
            throw new Exception("Error, LIMIT keyword cannot come before ORDER BY");
        }

        if (count($order) == 1 && in_array(strtoupper($order[0]), $orderBy)) {
            $concat = "ORDER BY " . implode(', ', $columns) . " " . $order[0] . " ";
        } else {
            $concat = "ORDER BY " . implode(', ', $columns) . " ASC " ;
        }
        $this->_sql .= $concat;

        return $this;
    }

    protected function limit (int $limit = 1) {
        if (stripos($this->_sql, 'SELECT') === false) {
            throw new Exception("Error, sql needs SELECT keyword");
        }

        $this->_sql .= "LIMIT $limit ";
        return $this;
    }

    protected function offset (int $offset = 0) {
        if (stripos($this->_sql, 'SELECT') === false) {
            throw new Exception("Error, sql needs SELECT keyword");
        }

        $this->_sql .= "OFFSET $offset";
        return $this;
    }

    protected function now () {
        return $this->query(trim($this->_sql), $this->_attributes);
    }

    protected function delete () {
        $this->_sql = "DELETE FROM " . static::$table_name . " ";
        return $this;
    }

    protected function update ($null_pairs = []) {
        $attributes = $this->update_attributes();

        $attribute_pairs = [];
        $set = [];

        if (count($null_pairs)) {
            foreach($null_pairs as $value) {
                $set[] = "{$value} = NULL";
            }
        }

        foreach($attributes as $key => $value) {
            $set[] = "{$key} = :{$key}";

            $key_ph = ':' . $key;
            $attribute_pairs[$key_ph] = $value;
        }

        $this->_sql = "UPDATE ". static::$table_name . " SET " . implode(', ', $set) . " ";
        $this->_attributes = $attribute_pairs;

        return $this;
    }

    protected function create() {
        $attributes = $this->update_attributes();

        if (!count($attributes)) {
            throw new Exception("Error, Fields are empty");
        }

        $attribute_pairs = [];
        foreach ($attributes as $key => $value) {
            $key_ph = ':' . $key;
            $attribute_pairs[$key_ph] = $value;
        }

        $this->_sql = "INSERT INTO " . static::$table_name . " (" . implode(', ', array_keys($attributes)) . ") VALUES (" . implode(', ', array_keys($attribute_pairs)) . ")";
        $this->_attributes = $attribute_pairs;

        return $this;
        // return $this->query($sql, $this->_attributes);
    }

//    protected function save(int $id = 0) {
//        // A new record will not have an ID yet
//        if(isset($id) && $id) {
//            return $this->update()->where(['id', '=', $id])->now();
//        } else {
//            return $this->create()->now();
//        }
//
//    }

    static protected function instantiate($record) {
        $object = new static;
        // Could manually assign values to properties
        // but automatically assignment is easier and re-usable
        foreach($record as $property => $value) {
            if(property_exists($object, $property)) {
                $object->$property = $value;
            }
        }
        return $object;
    }

    public function merge_attributes($args=[]) {
        foreach($args as $key => $value) {
            if(property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }

    // Properties which have database columns, excluding ID
    public function attributes() {
        $attributes = [];
        foreach(static::$db_columns as $column) {
            if($column == 'id') { continue; }
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }

    public function update_attributes() {
        $attributes = [];
        foreach(static::$db_columns as $column) {
            if($column == 'id') { continue; }
//            if(property_exists($this, $column) && !empty($this->$column)) {
            if(property_exists($this, $column) && isset($this->$column) && strlen($this->$column) > 0) {
                $attributes[$column] = $this->$column;
            }
        }
        return $attributes;
    }

    public function error () {
        return $this->_error;
    }

    public function count() {
        return $this->_count;
    }

    public function results () {
        return $this->_results;
    }

}

