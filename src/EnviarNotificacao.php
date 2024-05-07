<?php

namespace Kaiopostal\CRMFirebase;

class EnviarNotificacao
{
    private string $url_firebase;
    private        $client;

    public function __construct(string $url, $client)
    {
        $this->url_firebase = $url;
        $this->client       = $client;
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

            $response = $this->client::withHeaders($headers)->post($this->url_firebase, $data);

            array_push($messages_callback, [ 'user_token' => $user, 'callback' => $response ]);
        }

        return $messages_callback;
    }


    private function getGoogleAccessToken()
    {
        $client = new \Google\Client();        
        $client->setAuthConfig(config('objetivo.firebase_credentials'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();
        $token = $client->getAccessToken();

        return $token['access_token'];
    }


}
