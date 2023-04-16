<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ApiResource(
    extraProperties: [
        'standard_put' => true,
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['email' => 'exact'])]

class Customer extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:Customer', 'write:Customer'])]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:Customer', 'write:Customer'])]
    private ?string $social = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:Customer', 'write:Customer'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:Customer', 'write:Customer'])]
    private ?string $dateOfBirth = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read:Customer', 'write:Customer'])]
    private ?string $facebookId = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Winner::class)]
    private Collection $gains;

    public function __construct()
    {
        parent::__construct();
        $this->gains = new ArrayCollection();
    }


    public function __serialize(): array
    {
        return [
            $this->id,
            $this->address,
            $this->social,
            $this->phone,
            $this->dateOfBirth,
            $this->facebookId,
            $this->gains,
            parent::__serialize(),
        ];
    }

    public function __unserialize(array $data): void
    {
        [
            $this->id,
            $this->address,
            $this->social,
            $this->phone,
            $this->dateOfBirth,
            $this->facebookId,
            $this->gains,
            parent::__unserialize($data),
        ] = $data;
    }

    public function getId(): ?int
    {
        return parent::getId();
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getSocial(): ?string
    {
        return $this->social;
    }

    public function setSocial(string $social): self
    {
        $this->social = $social;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDateOfBirth(): string
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(string $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookId(?string $facebookId): self
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * @return Collection<int, Winner>
     */
    public function getGains(): Collection
    {
        return $this->gains;
    }

    public function addGain(Winner $gain): self
    {
        if (!$this->gains->contains($gain)) {
            $this->gains->add($gain);
            $gain->setCustomer($this);
        }

        return $this;
    }

    public function removeGain(Winner $gain): self
    {
        if ($this->gains->removeElement($gain)) {
            // set the owning side to null (unless already changed)
            if ($gain->getCustomer() === $this) {
                $gain->setCustomer(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return parent::__toString();
    }
}
