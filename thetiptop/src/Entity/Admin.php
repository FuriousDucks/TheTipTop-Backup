<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\AdminRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Doctrine\Odm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Tests\Fixtures\ApiResource\User;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ApiResource(
    extraProperties: [
        'standard_put' => true,
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['email' => 'exact'])]

class Admin extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['read:User', 'write:User'])]
    private ?bool $logged = null;

    public function getId(): ?int
    {
        return parent::getId();
    }

    public function isLogged(): ?bool
    {
        return $this->logged;
    }

    public function setLogged(bool $logged): self
    {
        $this->logged = $logged;

        return $this;
    }
}
