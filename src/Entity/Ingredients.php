<?php

namespace App\Entity;

use App\Repository\IngredientsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientsRepository::class)]
class Ingredients
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'ingredient_id', targetEntity: PlatIngredient::class)]
    private Collection $platIngredients;

    public function __construct()
    {
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
            $platIngredient->setIngredientId($this);
        }

        return $this;
    }

    public function removePlatIngredient(PlatIngredient $platIngredient): self
    {
        if ($this->platIngredients->removeElement($platIngredient)) {
            // set the owning side to null (unless already changed)
            if ($platIngredient->getIngredientId() === $this) {
                $platIngredient->setIngredientId(null);
            }
        }

        return $this;
    }
}
