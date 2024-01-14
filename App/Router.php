<?php

namespace App;

class Router
{
    public function behave()
    {
        if (empty($_GET)) {
            header('Location: ./?c=login&m=login');
            exit;
        }

        if (array_key_exists('code', $_GET) && !array_key_exists('c', $_GET)){
            header('Location: ' . '/?c=Lead&m=addLead&' . $_SERVER['QUERY_STRING']);
            exit;
        }

        $pathController = "\App\controllers";
        $controller = $pathController . "\\" . ucfirst($_GET['c']);
        $method = $_GET['m'];

        if (!class_exists($controller)){
            die("Некорректный запрос");
        }

        $class = new $controller();

        if (!method_exists($class,$method)){
            die("Некорректный запрос");
        }

        $class->$method();
    }
}