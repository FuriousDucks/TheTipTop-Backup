<?php

namespace App\Entity;

use App\Entity\Winner;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    extraProperties: [
        'standard_put' => true,
    ]
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Winner::class)]
    private Collection $winners;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img = null;

    public function __construct()
    {
        $this->winners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Winner>
     */
    public function getWinners(): Collection
    {
        return $this->winners;
    }

    public function addWinner(Winner $winner): self
    {
        if (!$this->winners->contains($winner)) {
            $this->winners->add($winner);
            $winner->setProduct($this);
        }

        return $this;
    }

    public function removeWinner(Winner $winner): self
    {
        if ($this->winners->removeElement($winner)) {
            // set the owning side to null (unless already changed)
            if ($winner->getProduct() === $this) {
                $winner->setProduct(null);
            }
        }

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
