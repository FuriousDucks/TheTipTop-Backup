<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Employee;
use App\Entity\Product;
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
        $manager->flush();
    }

    public function Users(ObjectManager $manager): void
    {
        // Admins
        $user = new Admin();
        $user->setEmail('benbrahim.elmahdi@gmail.com');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        // Employees
        $user = new Employee();
        $user->setEmail('ElMahdiBENBRAHIM@etu-digitalschool.paris');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_EMPLOYEE']);
        $manager->persist($user);

        // Users
        $user->setEmail('furious.duck.g4@gmail.com');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        // $user->setRoles(['ROLE_ADMIN']); // Default role is ROLE_USER
        $manager->persist($user);
    }

    public function Products(ObjectManager $manager): void
    {
        $product = [
            [
                'title' => 'Infuseur à thé',
                'description' => 'L\'infuseur est composé d\'acier inoxydable de qualité alimentaire.',
                'price' => 2.99,
                'image' => 'https://dsp-archiwebf22-eb-we-fh.fr/images/products/infuseur_the.webp'
            ],
            [
                'title' => 'Thé détox',
                'description' => 'Le thé détox est un mélange de thé vert et morceaux de fruits.',
                'price' => 4.99,
                'image' => 'https://dsp-archiwebf22-eb-we-fh.fr/images/products/the_detox_100.jpg'
            ],
            [
                'title' => 'Thé signature',
                'description' => 'Thé signature bio',
                'price' => 4.99,
                'image' => 'https://dsp-archiwebf22-eb-we-fh.fr/images/products/the-signature.jpg'
            ],
            [
                'title' => 'Coffret découverte d\'une valeur de 39€',
                'description' => 'Coffret découverte d\'une valeur de 39€',
                'price' => 39.99,
                'image' => 'https://dsp-archiwebf22-eb-we-fh.fr/images/products/coffret-decouverte-39.jpg'
            ],
            [
                'title' => 'Coffret découverte d\'une valeur de 69€',
                'description' => 'Coffret découverte d\'une valeur de 69€',
                'price' => 69.99,
                'image' => 'https://dsp-archiwebf22-eb-we-fh.fr/images/products/coffret-decouverte-69.jpg'
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
    }
}
