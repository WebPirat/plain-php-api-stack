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
        $user = new User();
        $user->name = $_POST['name'];
        $user->email = $_POST['email'];
        $user->password = $_POST['password'];
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
        $user->name = $_POST['name'];
        $user->email = $_POST['email'];
        $user->password = $_POST['password'];
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