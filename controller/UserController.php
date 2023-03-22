<?php
// include user modal
require_once __DIR__ . '/../modal/User.php';

//user controller bassed on the user modal handle api request
class UserController
{
    public function __construct()
    {
        $user = new User();
        $user->createTableIfNotExist('users', array());
    }

    public function index()
    {
        $user = new User();
        $users = $user->all();
        echo json_encode($users);
    }

    public function store()
    {
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password'])) {
            http_response_code(400);
            echo json_encode(array('error' => 'Name, Email, and Password are required.'));
            return;
        }

        $user = new User();
        $user->name = $_POST['name'];
        $user->email = $_POST['email'];
        $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user->save();
        echo json_encode($user);
    }

    public function show($id)
    {
        $user = new User();
        $user = $user->find($id);
        echo json_encode($user);
    }

    public function update($id)
    {
        $user = new User();
        $user = $user->find($id);

        if (isset($_POST['old_password']) && !password_verify($_POST['old_password'], $user->password)) {
            http_response_code(400);
            echo json_encode(array('error' => 'Old password is incorrect.'));
            return;
        }

        if (!empty($_POST['name'])) {
            $user->name = $_POST['name'];
        }

        if (!empty($_POST['email'])) {
            $user->email = $_POST['email'];
        }

        if (!empty($_POST['password'])) {
            $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $user->save();
        echo json_encode($user);
    }

    public function destroy($id)
    {
        $user = new User();
        $user = $user->find($id);
        $user->delete();
        echo json_encode($user);
    }
}
