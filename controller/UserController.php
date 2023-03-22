<?php
//user controller based on the router class
class UserController
{
    public function index()
    {
        $users = User::all();
        header('Content-Type: application/json');
        echo json_encode($users);
    }
    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            header('Content-Type: application/json');
            echo json_encode($user);
        } else {
            header('HTTP/1.1 404 Not Found');
            echo 'User not found.';
        }
    }
    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();
        header('HTTP/1.1 201 Created');
        header('Content-Type: application/json');
        echo json_encode(['id' => $user->id]);
    }
    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $user = User::find($id);
        if ($user) {
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->save();
            header('HTTP/1.1 204 No Content');
        } else {
            header('HTTP/1.1 404 Not Found');
            echo 'User not found.';
        }
    }
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            header('HTTP/1.1 204 No Content');
        } else {
            header('HTTP/1.1 404 Not Found');
            echo 'User not found.';
        }
    }
}