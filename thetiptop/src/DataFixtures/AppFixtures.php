<?php

namespace App\DataFixtures;

use App\Entity\ContestGame;
use App\Entity\Employee;
use App\Entity\Product;
use App\Entity\Store;
use App\Entity\Ticket;
use App\Entity\User;
use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->Users($manager);
        $this->Products($manager);
        $this->Stores($manager);
        $this->Contest($manager);
        $this->Tickets($manager);
    }

    public function Users(ObjectManager $manager): void
    {
        // Admins
        $user = new User();
        $user->setEmail('benbrahim.elmahdi@gmail.com');
        $user->setFirstName('EL MAHDI');
        $user->setLastName('Benbrahim');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        // Employees
        $user = new Employee();
        $user->setEmail('ElMahdiBENBRAHIM@etu-digitalschool.paris');
        $user->setFirstName('EL MAHDI');
        $user->setLastName('Benbrahim');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_EMPLOYEE']);
        $manager->persist($user);

        // Users
        $user = new Customer();
        $user->setEmail('furious.duck.g4@gmail.com');
        $user->setFirstName('Furious');
        $user->setLastName('Duck');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        // $user->setRoles(['ROLE_ADMIN']); // Default role is ROLE_USER
        $manager->persist($user);
        $manager->flush();
    }

    public function Products(ObjectManager $manager): void
    {
        $product = [
            [
                'title' => 'Infuseur à thé',
                'description' => 'L\'infuseur est composé d\'acier inoxydable de qualité alimentaire.',
                'price' => 2.99,
                'image' => 'infuseur_the.webp'
            ],
            [
                'title' => 'Thé détox',
                'description' => 'Le thé détox est un mélange de thé vert et morceaux de fruits.',
                'price' => 4.99,
                'image' => 'the_detox_100.jpg'
            ],
            [
                'title' => 'Thé signature',
                'description' => 'Thé signature bio',
                'price' => 4.99,
                'image' => 'the-signature.jpg'
            ],
            [
                'title' => 'Coffret découverte d\'une valeur de 39€',
                'description' => 'Coffret découverte d\'une valeur de 39€',
                'price' => 39.99,
                'image' => 'coffret-decouverte-39.jpg'
            ],
            [
                'title' => 'Coffret découverte d\'une valeur de 69€',
                'description' => 'Coffret découverte d\'une valeur de 69€',
                'price' => 69.99,
                'image' => 'coffret-decouverte-69.jpg'
            ]
        ];

        foreach ($product as $item) {
            $product = new Product();
            $product->setTitle($item['title']);
            $product->setDescription($item['description']);
            $product->setPrice($item['price']);
            $product->setImg($item['image']);
            $manager->persist($product);
        }

        $manager->flush();
    }

    public function Tickets(ObjectManager $manager): void
    {
        $batchSize = 20;
        for ($i = 1; $i <= 500; $i++) {
            $ticket = new Ticket();
            $ticketNumber = str_pad($i, 10, '0', STR_PAD_LEFT);
            $ticket->setNumber($ticketNumber);
            $ticket->setAmount(mt_rand(49, 100));
            $stores = $manager->getRepository(Store::class)->findAll();
            $ids = array_map(function ($store) {
                return $store->getId();
            }, $stores);
            $ticket->setStore($stores[array_rand($ids)]);
            $contest = $manager->getRepository(ContestGame::class)->find(1);
            $ticket->setContest($contest);
            $manager->persist($ticket);
            $manager->flush();
            if (($i % $batchSize) === 0) {
                $manager->flush();
                $manager->clear();
            }
        }
        $manager->flush();
        $manager->clear();
    }

    public function Stores(ObjectManager $manager): void
    {
        $store = [
            [
                'name' => 'Paris',
                'address' => '1 rue de la paix',
                'phone' => '01 23 45 67 89',
            ],
            [
                'name' => 'Lyon',
                'address' => '1 rue de la paix',
                'phone' => '01 23 45 67 89',
            ]
        ];

        foreach ($store as $item) {
            $store = new Store();
            $store->setName($item['name']);
            $store->setAddress($item['address']);
            $store->setTel($item['phone']);
            $manager->persist($store);
        }

        $manager->flush();
    }

    public function Contest(ObjectManager $manager): void
    {
        $contest = new ContestGame();
        $contest->setTitle('ThéTipTop');
        $contest->setStartDate(new \DateTime('2023-04-01'));
        $contest->setEndDate(new \DateTime('2023-4-30'));
        $contest->setMaxWinners(1500000);
        $manager->persist($contest);
        $manager->flush();
    }
}
