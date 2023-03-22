<?php


//user modal based on the db class
class User
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $created_at;
    public $updated_at;
    public function __construct()
    {
        $this->db = new Database();
    }
    public function all()
    {
        $results = $this->db->select('users', '*');
        $users = [];
        foreach ($results as $result) {
            $user = new User();
            $user->id = $result['id'];
            $user->name = $result['name'];
            $user->email = $result['email'];
            $user->password = $result['password'];
            $user->created_at = $result['created_at'];
            $user->updated_at = $result['updated_at'];
            $users[] = $user;
        }
        return $users;
    }
    public function find($id)
    {
        $result = $this->db->select('users', '*', "id = $id");
        if ($result) {
            $user = new User();
            $user->id = $result[0]['id'];
            $user->name = $result[0]['name'];
            $user->email = $result[0]['email'];
            $user->password = $result[0]['password'];
            $user->created_at = $result[0]['created_at'];
            $user->updated_at = $result[0]['updated_at'];
            return $user;
        } else {
            return false;
        }
    }
    public function save()
    {
        $data = array(
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password
        );
        if ($this->id) {
            $this->db->update('users', $data, "id = $this->id");
        } else {
            $this->db->insert('users', $data);
            $this->id = $this->db->lastInsertId();
        }
    }
    public function delete()
    {
        $this->db->delete('users', "id = $this->id");
    }

    // function to create the user table in database
    public function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT(11) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        )";
        $this->db->query($sql);
    }
}
