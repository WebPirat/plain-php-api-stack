<?php
//***********************************************************************************
// File name: DB.php
// Class name: DB
// Description: This class is used to connect to the database and perform queries.
//              It is a singleton class, so only one instance of the class can be
//              created.

// class for crud operations in plain php with mysqli

class DB
{
    private static $instance = null;
    private $mysqli,
        $host = 'localhost',
        $username = 'root',
        $password = '',
        $db_name = 'test';

    private function __construct()
    {
        $this->mysqli = new mysqli($this->host, $this->username, $this->password, $this->db_name);
        if ($this->mysqli->connect_error) {
            die('Connect Error (' . $this->mysqli->connect_errno . ') ' . $this->mysqli->connect_error);
        }
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function query($sql)
    {
        $result = $this->mysqli->query($sql);
        if (!$result) {
            die('There was an error running the query [' . $this->mysqli->error . ']');
        }
        return $result;
    }

    public function select($table, $rows = '*', $where = null, $order = null)
    {
        $q = 'SELECT ' . $rows . ' FROM ' . $table;
        if ($where != null) {
            $q .= ' WHERE ' . $where;
        }
        if ($order != null) {
            $q .= ' ORDER BY ' . $order;
        }
        $result = $this->mysqli->query($q);
        if (!$result) {
            die('There was an error running the query [' . $this->mysqli->error . ']');
        }
        $numRows = $result->num_rows;
        for ($i = 0; $i < $numRows; $i++) {
            $row = $result->fetch_assoc();
            $key = array_keys($row);
            for ($x = 0; $x < count($key); $x++) {
                if (!is_int($key[$x])) {
                    if ($result->num_rows >= 1) {
                        $newArray[$i][$key[$x]] = $row[$key[$x]];
                    } else if ($result->num_rows < 1) {
                        $newArray = null;
                    } else {
                        $newArray[$key[$x]] = $row[$key[$x]];
                    }
                }
            }
        }
        return $newArray;
    }
}
