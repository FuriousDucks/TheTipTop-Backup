<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

class LoginTest extends PantherTestCase
{
    public function testLoginPage(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/login');
        $this->assertPageTitleContains('Connexion');
        $this->assertSelectorTextContains('h1', 'Connexion');
    }
}
