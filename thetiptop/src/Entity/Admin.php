<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AdminRepository;
use ApiPlatform\Metadata\Tests\Fixtures\ApiResource\User;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ApiResource(
    extraProperties: [
        'standard_put' => true,
    ]
)]
class Admin extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
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
