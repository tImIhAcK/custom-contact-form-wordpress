<?php

class HubSpotIntegration
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function createContact(string $firstname, string $lastname, string $email, string $message): array
    {
        $hubspotApiKey = $this->config['HUBSPOT_API_KEY'];
        $hubspotApiUrl = 'https://api.hubapi.com/crm/v3/objects/contact';

        $data = [
            'properties' => [
                "email" => $email,
                "firstname" => $firstname,
                "lastname" => $lastname,
                "message" => $message
            ]
        ];

        $response = $this->makeRequest($hubspotApiUrl, $hubspotApiKey, $data);

        return $response;
    }

    private function makeRequest(string $url, string $apiKey, array $data): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $apiKey,
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}
