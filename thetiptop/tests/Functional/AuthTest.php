<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    public function testAuth(): void
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form();

        $form['email'] = 'furious.duck.g4@gmail.com';
        $form['password'] = 'password';

        $client->submit($form);

        $this->assertResponseRedirects('/');

        $client->followRedirect();

        $this->assertSelectorTextContains('h1', 'ThéTipTop');

        $client->request('GET', '/logout');

        $crawler = $client->request('GET', '/login');

        $this->assertSelectorTextContains('h1', 'Connexion');
    }
}
