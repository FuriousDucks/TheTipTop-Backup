<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
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
        return $this->id;
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
