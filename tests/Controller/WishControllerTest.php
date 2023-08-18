<?php

namespace App\Tests\Controller;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WishControllerTest extends WebTestCase
{

    public function routes(): Generator {
        yield "Page d'accueil" => ["GET", "/"];
        yield "Page des wishes" => ["GET", "/login"];
    }

    // la variable $methode est le premier élément du tableau de la foncion routes soit 'GET'
    // la variable $url est le deuxième élément du tableau de la fonction routes soit "/" ou "/wishes/
    /**
     * @dataProvider routes
     */
    public function testRoutes(
        string $methode,
        string $url
    ): void
    {
        $client = static::createClient();
        $client->request($methode, $url);

        $this->assertResponseIsSuccessful();
    }

    // Test failure car pas de <h1> avec le text Hello World
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello World');
    }
}
