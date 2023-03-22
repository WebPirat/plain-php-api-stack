<?php
// database class to connect to the database

class Database
{
    private $host = "co-pilot-repo-mysql-1";
    private $user = "sbx";
    private $password = "sbx";
    private $database = "sbx";
    protected $connection;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $this->connection = mysqli_connect($this->host, $this->user, $this->password, $this->database);

        if (!$this->connection) {
            die("Verbindung zur Datenbank fehlgeschlagen: " . mysqli_connect_error());
        }
    }

    public function select($table, $conditions = array(), $order_by = null, $limit = null) {
        $query = "SELECT * FROM " . $table;

        if (!empty($conditions)) {
            $query .= " WHERE ";
            $i = 0;

            foreach ($conditions as $key => $value) {
                if ($i > 0) {
                    $query .= " AND ";
                }

                if (strpos($key, '>') !== false) {
                    $query .= str_replace('>', '', $key) . ">?";
                } else if (strpos($key, '<') !== false) {
                    $query .= str_replace('<', '', $key) . "<?";
                } else {
                    $query .= $key . "=?";
                }

                $i++;
            }
            $query .= " AND deleted_At IS NULL";
        } else {
            $query .= " WHERE deleted_At IS NULL";
        }
        //where add deleted_At is null

        if ($order_by) {
            $query .= " ORDER BY " . $order_by;
        }

        if ($limit) {
            $query .= " LIMIT " . $limit;
        }

        $stmt = mysqli_prepare($this->connection, $query);

        if (!$stmt) {
            die("Fehler beim Erstellen des Prepared Statements: " . mysqli_error($this->connection));
        }

        if (!empty($conditions)) {
            $params = array_values($conditions);
            $types = str_repeat('s', count($params));
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            die("Fehler beim Ausführen der Abfrage: " . mysqli_error($this->connection));
        }

        $rows = array();

        $result_set = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result_set)) {
            $rows[] = $row;
        }

        mysqli_stmt_close($stmt);

        return $rows;
    }


    public function insert($table, $data)
    {
        $keys = array();
        $values = array();

        foreach ($data as $key => $value) {
            $keys[] = $key;
            $values[] = "?";
        }

        $query = "INSERT INTO " . $table . " (" . implode(",", $keys) . ") VALUES (" . implode(",", $values) . ")";

        $stmt = mysqli_prepare($this->connection, $query);

        if (!$stmt) {
            die("Fehler beim Erstellen des Prepared Statements: " . mysqli_error($this->connection));
        }

        $params = array_values($data);
        $types = str_repeat('s', count($params));

        mysqli_stmt_bind_param($stmt, $types, ...$params);

        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            die("Fehler beim Ausführen der Abfrage: " . mysqli_error($this->connection));
        }

        return mysqli_insert_id($this->connection);
    }

    public function update($table, $data, $conditions = array())
    {
        $update_data = array();

        foreach ($data as $key => $value) {
            $update_data[] = $key . "=?";
        }

        $query = "UPDATE " . $table . " SET " . implode(",", $update_data);

        if (!empty($conditions)) {
            $query .= " WHERE ";
            $i = 0;

            foreach ($conditions as $key => $value) {
                if ($i > 0) {
                    $query .= " AND ";
                }

                $query .= $key . "=?";
                $i++;
            }
        }
    }
    public function delete($table, $id) {
        $query = "UPDATE " . $table . " SET deleted_at = NOW() WHERE id = ?";

        $stmt = mysqli_prepare($this->connection, $query);

        if (!$stmt) {
            die("Fehler beim Erstellen des Prepared Statements: " . mysqli_error($this->connection));
        }

        mysqli_stmt_bind_param($stmt, "i", $id);

        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            die("Fehler beim Ausführen der Abfrage: " . mysqli_error($this->connection));
        }

        mysqli_stmt_close($stmt);

        return true;
    }

    public function createTable($table, $data)
    {
        // Add id column to beginning of data array
        $data = array_merge(array('ID' => "INT(11) NOT NULL AUTO_INCREMENT"), $data);
        // always add created_At, updated_At, deleted_At to datadas
        $data['created_At'] = "TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $data['updated_At'] = "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        $data['deleted_At'] = "TIMESTAMP NULL DEFAULT NULL";

        // CREATE TABLE-Anweisung erstellen
        $query = "CREATE TABLE IF NOT EXISTS $table (";

        // Spalteninformationen hinzufügen
        foreach ($data as $column_name => $column_data) {
            $query .= "$column_name $column_data, ";
        }

        // Letzte Komma entfernen
        $query = rtrim($query, ", ");

        // Primary key hinzufügen
        $query .= ", PRIMARY KEY (ID))";

        // Query ausführen
        $result = mysqli_query($this->connection, $query);

        if (!$result) {
            die("Fehler beim Ausführen der Abfrage: " . mysqli_error($this->connection));
        }

        return mysqli_insert_id($this->connection);
    }

}
