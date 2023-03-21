<?php

class Database
{
    private $host;
    private $username;
    private $password;
    private $database;
    private $connection;

    public function __construct($host, $username, $password, $database)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->connect();
    }

    private function connect()
    {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function query($sql)
    {
        return $this->connection->query($sql);
    }

    public function prepare($sql)
    {
        return $this->connection->prepare($sql);
    }

    public function escape($value)
    {
        return $this->connection->real_escape_string($value);
    }

    public function getLastInsertId()
    {
        return $this->connection->insert_id;
    }

    public function close()
    {
        $this->connection->close();
    }

    public function select($table, $where = null, $orderBy = null, $limit = null)
    {
        $sql = "SELECT * FROM " . $table;

        if ($where) {
            $sql .= " WHERE " . $where;
        }

        if ($orderBy) {
            $sql .= " ORDER BY " . $orderBy;
        }

        if ($limit) {
            $sql .= " LIMIT " . $limit;
        }

        $result = $this->query($sql);

        $rows = array();

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function insert($table, $data)
    {
        $fields = array();
        $values = array();

        foreach ($data as $field => $value) {
            $fields[] = $field;
            $values[] = "'" . $this->escape($value) . "'";
        }

        $sql = "INSERT INTO " . $table . " (" . implode(",", $fields) . ") VALUES (" . implode(",", $values) . ")";

        $this->query($sql);

        return $this->getLastInsertId();
    }

    public function update($table, $data, $where)
    {
        $set = array();

        foreach ($data as $field => $value) {
            $set[] = $field . "='" . $this->escape($value) . "'";
        }

        $sql = "UPDATE " . $table . " SET " . implode(",", $set) . " WHERE " . $where;

        return $this->query($sql);
    }

    public function delete($table, $where)
    {
        $sql = "DELETE FROM " . $table . " WHERE " . $where;

        return $this->query($sql);
    }
}