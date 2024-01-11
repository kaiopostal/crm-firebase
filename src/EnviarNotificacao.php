<?php

namespace Kaiopostal\CRMFirebase;

use GuzzleHttp\Client;

class EnviarNotificacao
{
    const BASE_ENDPOINT = 'https://fcm.googleapis.com/v1/projects/push-notification-2cb1f/messages:send';
    public function __construct(
        private  Client $client = new Client()
    ){}

    private function enviaNotificacao(array $conteudo, array $userTokens): array
    {
        $messages_callback = [];

        foreach ($userTokens as $user) {
            $data = [
                "message" => [
                    "token"        => $user['token'],
                    "notification" => [
                        "title" => $conteudo['titulo'],
                        "body"  => $conteudo['descricao']
                    ]
                ]
            ];


            $response = $this->client->post(self::BASE_ENDPOINT, [
                'body' => [$data],
                'headers' => [
                    'Authorization' => 'Bearer ' . static::getGoogleAccessToken(),
                    'Content-Type'  => 'application/json'
                ]
            ])->getBody()->getContents();

            array_push($messages_callback, ['user_token' => $user, 'callback' => $response]);
        }


        return $messages_callback;
    }


    private static function getGoogleAccessToken()
    {
        $client = new \Google\Client();
        $client->useApplicationDefaultCredentials(); // Recupera o valor da variável GOOGLE_APPLICATION_CREDENTIALS no .env
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();
        $token = $client->getAccessToken();

        return $token['access_token'];
    }



}
