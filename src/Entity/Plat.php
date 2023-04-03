<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'plats')]
    private ?restaurant $restaurant_id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\OneToMany(mappedBy: 'plat_id', targetEntity: MenuPlat::class)]
    private Collection $menuPlats;

    #[ORM\OneToMany(mappedBy: 'plat_id', targetEntity: PlatIngredient::class)]
    private Collection $platIngredients;

    public function __construct()
    {
        $this->menuPlats = new ArrayCollection();
        $this->platIngredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRestaurantId(): ?restaurant
    {
        return $this->restaurant_id;
    }

    public function setRestaurantId(?restaurant $restaurant_id): self
    {
        $this->restaurant_id = $restaurant_id;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, MenuPlat>
     */
    public function getMenuPlats(): Collection
    {
        return $this->menuPlats;
    }

    public function addMenuPlat(MenuPlat $menuPlat): self
    {
        if (!$this->menuPlats->contains($menuPlat)) {
            $this->menuPlats->add($menuPlat);
            $menuPlat->setPlatId($this);
        }

        return $this;
    }

    public function removeMenuPlat(MenuPlat $menuPlat): self
    {
        if ($this->menuPlats->removeElement($menuPlat)) {
            // set the owning side to null (unless already changed)
            if ($menuPlat->getPlatId() === $this) {
                $menuPlat->setPlatId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PlatIngredient>
     */
    public function getPlatIngredients(): Collection
    {
        return $this->platIngredients;
    }

    public function addPlatIngredient(PlatIngredient $platIngredient): self
    {
        if (!$this->platIngredients->contains($platIngredient)) {
            $this->platIngredients->add($platIngredient);
            $platIngredient->setPlatId($this);
        }

        return $this;
    }

    public function removePlatIngredient(PlatIngredient $platIngredient): self
    {
        if ($this->platIngredients->removeElement($platIngredient)) {
            // set the owning side to null (unless already changed)
            if ($platIngredient->getPlatId() === $this) {
                $platIngredient->setPlatId(null);
            }
        }

        return $this;
    }
}
