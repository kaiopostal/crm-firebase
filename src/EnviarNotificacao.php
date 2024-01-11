<?php

namespace Kaiopostal\CRMFirebase;

use Illuminate\Support\Facades\Http;

class EnviarNotificacao
{
    private string $url_firebase;

    public function __construct(string $url)
    {
        $this->url_firebase = $url;
    }

    public function enviaNotificacao(array $conteudo, array $userTokens): array
    {
        $messages_callback = [];

        foreach ($userTokens as $user) {
            $data = [
                "message" => [
                    "token"        => $user['token'],
                    "notification" => [
                        "title" => $conteudo['title'],
                        "body"  => $conteudo['body']
                    ]
                ]
            ];

            $headers = [
                'Authorization' => 'Bearer ' . $this->getGoogleAccessToken(),
                'Content-Type'  => 'application/json'
            ];

            $cliente = new Http();

            $response = $cliente->withHeaders($headers)->post($this->url_firebase, $data);

            array_push($messages_callback, [ 'user_token' => $user, 'callback' => $response ]);
        }

        return $messages_callback;
    }


    private function getGoogleAccessToken()
    {
        $client = new \Google\Client();
        $client->useApplicationDefaultCredentials(); // Recupera o valor da variável GOOGLE_APPLICATION_CREDENTIALS no .env
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();
        $token = $client->getAccessToken();

        return $token['access_token'];
    }


}
