<?php

class HubSpotIntegration
{
    public static function createContact($config, $firstname, $lastname, $email, $message)
    {
        $hubspot_api_key = $config['key']['HUBSPOT_API_KEY'];
        $hubspot_api_url = 'https://api.hubapi.com/crm/v3/objects/contact';

        $data = [
            'properties' => [
                "email" => $email,
                "firstname" => $firstname,
                "lastname" => $lastname,
                "message" => $message
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $hubspot_api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $hubspot_api_key,
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $responseData = json_decode($response, true);

        return $responseData;
    }
}
