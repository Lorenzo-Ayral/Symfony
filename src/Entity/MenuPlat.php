<?php

namespace App\Entity;

use App\Repository\MenuPlatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuPlatRepository::class)]
class MenuPlat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'menuPlats')]
    private ?menu $menu_id = null;

    #[ORM\ManyToOne(inversedBy: 'menuPlats')]
    private ?plat $plat_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenuId(): ?menu
    {
        return $this->menu_id;
    }

    public function setMenuId(?menu $menu_id): self
    {
        $this->menu_id = $menu_id;

        return $this;
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
}
