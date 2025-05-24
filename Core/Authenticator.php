<?php

namespace Core;

class Authenticator
{
    /**
     * @param $email
     * @param $password
     * @return bool
     */
    public function attempt($email, $password): bool
    {
        $user = App::resolve(Database::class)->query('select * from users where email = :email', [
            'email' => $email
        ])->find();

        if($user) {
            if(password_verify($password, $user['password'])) {
                $this->login([
                    'email' => $email
                ]);

                return true;
            }
        }

        return false;
    }

    /**
     * @param $user
     * @return void
     */
    public function login($user): void
    {
        $_SESSION['user'] = [
            'email' => $user['email'],
        ];

        session_regenerate_id(true);
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        Session::destroy();
    }

}