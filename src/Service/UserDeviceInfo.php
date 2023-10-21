<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserDeviceInfo {

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    private function getArrayResponse() : array
    {
         $response = $this->client->request(
            'GET',
            'http://ipinfo.io/json'
        );
        return $response->toArray();
    }

    public function getIp(): string
    {
        return $this->getArrayResponse()["ip"];
    }

    public function getCountryCode(){
        return $this->getArrayResponse()["country"];
    }

}