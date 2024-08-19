<?php

namespace App\Service;

use GuzzleHttp\Client;

class JokeApiService
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = new Client(['base_uri' => 'https://api.chucknorris.io/']); 
    }

    public function getCategories(): array
    {
        $response = $this->client->request('GET', 'jokes/categories');
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getRandomJoke(string $category): string
    {
        $response = $this->client->request('GET', 'jokes/random', [
            'query' => ['category' => $category]
        ]);
        return json_decode($response->getBody()->getContents(), true)['value'];
    }
}

