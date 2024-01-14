<?php

namespace App\controllers;

use AmoCRM\Client\AmoCRMApiClient;
use Twig;
use Twig\Environment;

class BaseController
{
    protected static $clientId = 'c407eb23-9000-4103-8a76-cf22a154a104';
    protected static $clientSecret = 'CuIVLZ3c3oz7RPEk5KnIjqQduzWmVdIk9aNkyRefbQZSDvlrdoUO4Vv1UObGubOm';
    protected static $redirectUri = 'http://testovoeem.ru.swtest.ru';
    protected AmoCRMApiClient $apiClient;
    protected $accessToken;
    private Environment $twig;

    public function __construct($auth = true)
    {
        $loader = new Twig\Loader\FilesystemLoader(__DIR__ . '/../views/' . $this->getDir());
        $this->twig = new Environment($loader);
        $this->apiClient = new AmoCRMApiClient(self::$clientId, self::$clientSecret, self::$redirectUri);
        $this->apiClient->setAccountBaseDomain("katerina53.amocrm.ru");
    }

    public function render(string $template, array $data)
    {
        echo $this->twig->render($template . '.html.twig', $data);
    }

    protected function getToken(): void
    {
        try {
            $accessToken = $this->apiClient->getOAuthClient()->getAccessTokenByCode($_GET['code']);
            $this->apiClient->setAccessToken($accessToken);

            if (!$accessToken->hasExpired()) {
                $this->saveToken([
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $this->apiClient->getAccountBaseDomain(),
                ]);
            }
        } catch (Exception $e) {
            print_r($e);
            die((string)$e);
        }
    }

    protected function saveToken($accessToken): void
    {
        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];

            file_put_contents(__DIR__ . '/../services/token.txt', json_encode($data));
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }
    
    private function getDir(): string
    {
        $class = substr(strrchr(get_called_class(), "\\"), 1);
        return str_replace('controller', '', strtolower($class));
    }
}