<?php
include_once __DIR__ . '/../classes/Database.php';

//user modal based on the database class
class User extends Database
{
    public $id;
    public $name;
    public $email;
    public $password;

    public function all()
    {
        $users = $this->select('users');
        return $users;
    }

    public function find($id)
    {
        $user = $this->select('users', array('id' => $id));
        return $user[0];
    }

    public function save()
    {
        $data = array(
            'name' => $this->name,
            'email' => $this->email,
            'password' => password_hash($this->password, PASSWORD_DEFAULT),
        );

        if ($this->id) {
            $this->update('users', $data, array('id' => $this->id));
        } else {
            $this->insert('users', $data);
            $this->id = $this->connection->insert_id;
        }
    }

    public function delete($table, $id)
    {
        $this->delete('users', $id);
    }

    //function to create user table in mysql
    public function createTableIfNotExist()
    {
        //$data von createTable in Database.php
        $data = array(
            'name' => 'VARCHAR(255) NOT NULL',
            'email' => 'VARCHAR(255) NOT NULL',
            'password' => 'VARCHAR(255) NOT NULL',
        );
        $this->createTable('users', $data);
    }


}

