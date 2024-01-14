<?php

namespace App\controllers;
class Login extends BaseController
{
    public function login(): void
    {
        session_start();
        
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth2state'] = $state;
        $data = ['clientId' => self::$clientId, 'state' => $state];
        
        $this->render('login', $data);
    }
}