<?php

namespace VandersonRamos\FMTransportes\Services;

use GuzzleHttp\Client;

class CarrierHttpClient
{
    const HTTP_OK = 200;
    const LIVE = 'http://api.frete.alfatracking.com.br/';
    const SANDBOX = 'http://homolog.api.frete.alfatracking.com.br/';

    /**
     * Retrieve the login
     * @return string
     */
    private function getLogin(): string
    {
        return core()->getConfigData('sales.carriers.vandersonramos_fmtransportes.login');
    }

    /**
     * Retrieve the password
     * @return string
     */
    private function getPassword(): string
    {
        return core()->getConfigData('sales.carriers.vandersonramos_fmtransportes.password');
    }

    /**
     * Retrieve the environment
     * @return string
     */
    private function getEnvironment(): string
    {
        $env = core()->getConfigData('sales.carriers.vandersonramos_fmtransportes.password');

        if ($env === 'live') {
            return self::LIVE;
        }

        return self::SANDBOX;
    }

    /**
     * @param $data
     * @param $url
     * @return array
     */
    private function doRequest(array $data, string $url): array
    {
        $errorReturn = [
            'status' => 0,
            'response_message' => 'Not available at the moment.'
        ];

        try {
            $client = new Client(['base_uri' => $this->getEnvironment()]);

            $response = $client->request(
                'POST',
                $this->getEnvironment() . $url,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => "Basic " . base64_encode($this->getLogin() . ':' . $this->getPassword())
                    ],
                    'body' => json_encode($data)
                ]
            );

            if ($response->getStatusCode() === self::HTTP_OK) {
                return [
                    'status' => 1,
                    'response_message' => json_decode($response->getBody())
                ];
            }

            return $errorReturn;

        } catch (\Exception $e) {
            return $errorReturn;
        }
    }

    /**
     * Quote request
     * @param $data
     * @return array
     */
    public function requestQuote(array $data): array
    {
        return $this->doRequest($data, 'v1/shipping/quote');
    }
}