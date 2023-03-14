<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrdredItem::class)]
    private Collection $ordredItems;

    public function __construct()
    {
        $this->ordredItems = new ArrayCollection();
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, OrdredItem>
     */
    public function getOrdredItems(): Collection
    {
        return $this->ordredItems;
    }

    public function addOrdredItem(OrdredItem $ordredItem): self
    {
        if (!$this->ordredItems->contains($ordredItem)) {
            $this->ordredItems->add($ordredItem);
            $ordredItem->setProduct($this);
        }

        return $this;
    }

    public function removeOrdredItem(OrdredItem $ordredItem): self
    {
        if ($this->ordredItems->removeElement($ordredItem)) {
            // set the owning side to null (unless already changed)
            if ($ordredItem->getProduct() === $this) {
                $ordredItem->setProduct(null);
            }
        }

        return $this;
    }
}
