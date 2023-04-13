<?php

namespace App\Tests\Unit;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
class ApiLoginTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testLogin(): void
    {
        $client = self::createClient();
        $container = self::getContainer();

        $user = new User();
        $user->setEmail('benbrahim.elmahdi@gmail.com');
        $user->setFirstName('EL MAHDI');
        $user->setLastName('Benbrahim');
        $user->setIsVerified(true);

        $user->setPassword(
            $container->get('security.user_password_hasher')->hashPassword($user, 'password')
        );
        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        $response = $client->request('POST', '/api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'benbrahim.elmahdi@gmail.com',
                'password' => 'password',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);
    }
}
