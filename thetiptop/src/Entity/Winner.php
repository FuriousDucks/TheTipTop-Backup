<?php

namespace App\Entity;

use App\Repository\WinnerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WinnerRepository::class)]
class Winner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOfDraw = null;

    #[ORM\ManyToOne(inversedBy: 'winners')]
    private ?Product $product = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne(inversedBy: 'gains')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\Column]
    private ?bool $recovered = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateOfDraw(): ?\DateTimeInterface
    {
        return $this->dateOfDraw;
    }

    public function setDateOfDraw(\DateTimeInterface $dateOfDraw): self
    {
        $this->dateOfDraw = $dateOfDraw;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function isRecovered(): ?bool
    {
        return $this->recovered;
    }

    public function setRecovered(bool $recovered): self
    {
        $this->recovered = $recovered;

        return $this;
    }
}
