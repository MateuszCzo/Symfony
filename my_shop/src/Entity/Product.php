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

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Image $image = null;

    #[ORM\ManyToMany(targetEntity: Category::class)]
    private Collection $subcategories;

    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'product', cascade: ['persist', 'remove'])]
    private Collection $otherImages;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Discount::class, mappedBy: 'products')]
    private Collection $discounts;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToMany(targetEntity: Attatchment::class)]
    private Collection $attatchments;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Manufacturer $manufacturer = null;

    public function __construct()
    {
        $this->subcategories = new ArrayCollection();
        $this->otherImages = new ArrayCollection();
        $this->discounts = new ArrayCollection();
        $this->attatchments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getSubcategories(): Collection
    {
        return $this->subcategories;
    }

    public function addSubcategory(Category $subcategory): static
    {
        if (!$this->subcategories->contains($subcategory)) {
            $this->subcategories->add($subcategory);
        }

        return $this;
    }

    public function removeSubcategory(Category $subcategory): static
    {
        $this->subcategories->removeElement($subcategory);

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getOtherImages(): Collection
    {
        return $this->otherImages;
    }

    public function addOtherImage(Image $otherImage): static
    {
        if (!$this->otherImages->contains($otherImage)) {
            $this->otherImages->add($otherImage);
            $otherImage->setProduct($this);
        }

        return $this;
    }

    public function removeOtherImage(Image $otherImage): static
    {
        if ($this->otherImages->removeElement($otherImage)) {
            // set the owning side to null (unless already changed)
            if ($otherImage->getProduct() === $this) {
                $otherImage->setProduct(null);
            }
        }

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Discount>
     */
    public function getDiscounts(): Collection
    {
        return $this->discounts;
    }

    public function addDiscount(Discount $discount): static
    {
        if (!$this->discounts->contains($discount)) {
            $this->discounts->add($discount);
            $discount->addProduct($this);
        }

        return $this;
    }

    public function removeDiscount(Discount $discount): static
    {
        if ($this->discounts->removeElement($discount)) {
            $discount->removeProduct($this);
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Attatchment>
     */
    public function getAttatchments(): Collection
    {
        return $this->attatchments;
    }

    public function addAttatchment(Attatchment $attatchment): static
    {
        if (!$this->attatchments->contains($attatchment)) {
            $this->attatchments->add($attatchment);
        }

        return $this;
    }

    public function removeAttatchment(Attatchment $attatchment): static
    {
        $this->attatchments->removeElement($attatchment);

        return $this;
    }

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?Manufacturer $manufacturer): static
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }
}
