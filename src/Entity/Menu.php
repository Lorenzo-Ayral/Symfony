<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?restaurant $restaurant_id = null;

    #[ORM\OneToMany(mappedBy: 'menu_id', targetEntity: MenuPlat::class)]
    private Collection $menuPlats;

    public function __construct()
    {
        $this->menuPlats = new ArrayCollection();
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

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
            $menuPlat->setMenuId($this);
        }

        return $this;
    }

    public function removeMenuPlat(MenuPlat $menuPlat): self
    {
        if ($this->menuPlats->removeElement($menuPlat)) {
            // set the owning side to null (unless already changed)
            if ($menuPlat->getMenuId() === $this) {
                $menuPlat->setMenuId(null);
            }
        }

        return $this;
    }
}
