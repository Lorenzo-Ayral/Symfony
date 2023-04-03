<?php

namespace App\Entity;

use App\Repository\PlatIngredientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatIngredientRepository::class)]
class PlatIngredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'platIngredients')]
    private ?plat $plat_id = null;

    #[ORM\ManyToOne(inversedBy: 'platIngredients')]
    private ?ingredients $ingredient_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlatId(): ?plat
    {
        return $this->plat_id;
    }

    public function setPlatId(?plat $plat_id): self
    {
        $this->plat_id = $plat_id;

        return $this;
    }

    public function getIngredientId(): ?ingredients
    {
        return $this->ingredient_id;
    }

    public function setIngredientId(?ingredients $ingredient_id): self
    {
        $this->ingredient_id = $ingredient_id;

        return $this;
    }
}
