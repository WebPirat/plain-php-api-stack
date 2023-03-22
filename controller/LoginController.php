<?php
//LoginController who uses the User modal and the JWT class to handle the login and logout
class LoginController
{
    public function login()
    {
        if (empty($_POST['email']) || empty($_POST['password'])) {
            http_response_code(400);
            echo json_encode(array('error' => 'Email and Password are required.'));
            return;
        }

        $user = new User();
        $user = $user->find_by_email($_POST['email']);

        if (!$user) {
            http_response_code(400);
            echo json_encode(array('error' => 'Email or Password is incorrect.'));
            return;
        }

        if (!password_verify($_POST['password'], $user->password)) {
            http_response_code(400);
            echo json_encode(array('error' => 'Email or Password is incorrect.'));
            return;
        }

        $jwt = new JWT();
        $token = $jwt->encode(array('id' => $user->id));

        setcookie('token', $token, time() + 60 * 60 * 24 * 7, '/');

        echo json_encode(array('token' => $token));
    }

    public function logout()
    {
        setcookie('token', '', time() - 3600, '/');
        header('Location: /login');
    }
}



