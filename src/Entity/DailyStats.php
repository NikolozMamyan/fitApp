<?php

namespace App\Entity;

use App\Repository\DailyStatsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DailyStatsRepository::class)]
class DailyStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $calories = null;

    #[ORM\Column(nullable: true)]
    private ?int $proteins = null;

    #[ORM\Column(nullable: true)]
    private ?int $glucides = null;

    #[ORM\Column(nullable: true)]
    private ?int $lipides = null;

    #[ORM\Column(nullable: true)]
    private ?int $vitamineC = null;

    #[ORM\Column(nullable: true)]
    private ?int $fibres = null;

    #[ORM\Column(nullable: true)]
    private ?int $eau = null;

    #[ORM\ManyToOne(inversedBy: 'dailyStats')]
    private ?UserData $userDailyStats = null;

    /**
     * @var Collection<int, FoodConsumed>
     */
    #[ORM\OneToMany(targetEntity: FoodConsumed::class, mappedBy: 'DailyStats')]
    private Collection $foodConsumeds;

    public function __construct()
    {
        $this->foodConsumeds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCalories(): ?int
    {
        return $this->calories;
    }

    public function setCalories(?int $calories): static
    {
        $this->calories = $calories;

        return $this;
    }

    public function getProteins(): ?int
    {
        return $this->proteins;
    }

    public function setProteins(?int $proteins): static
    {
        $this->proteins = $proteins;

        return $this;
    }

    public function getGlucides(): ?int
    {
        return $this->glucides;
    }

    public function setGlucides(?int $glucides): static
    {
        $this->glucides = $glucides;

        return $this;
    }

    public function getLipides(): ?int
    {
        return $this->lipides;
    }

    public function setLipides(?int $lipides): static
    {
        $this->lipides = $lipides;

        return $this;
    }

    public function getVitamineC(): ?int
    {
        return $this->vitamineC;
    }

    public function setVitamineC(?int $vitamineC): static
    {
        $this->vitamineC = $vitamineC;

        return $this;
    }

    public function getFibres(): ?int
    {
        return $this->fibres;
    }

    public function setFibres(?int $fibres): static
    {
        $this->fibres = $fibres;

        return $this;
    }

    public function getEau(): ?int
    {
        return $this->eau;
    }

    public function setEau(?int $eau): static
    {
        $this->eau = $eau;

        return $this;
    }

    public function getUserDailyStats(): ?UserData
    {
        return $this->userDailyStats;
    }

    public function setUserDailyStats(?UserData $userDailyStats): static
    {
        $this->userDailyStats = $userDailyStats;

        return $this;
    }

    /**
     * @return Collection<int, FoodConsumed>
     */
    public function getFoodConsumeds(): Collection
    {
        return $this->foodConsumeds;
    }

    public function addFoodConsumed(FoodConsumed $foodConsumed): static
    {
        if (!$this->foodConsumeds->contains($foodConsumed)) {
            $this->foodConsumeds->add($foodConsumed);
            $foodConsumed->setDailyStats($this);
        }

        return $this;
    }

    public function removeFoodConsumed(FoodConsumed $foodConsumed): static
    {
        if ($this->foodConsumeds->removeElement($foodConsumed)) {
            // set the owning side to null (unless already changed)
            if ($foodConsumed->getDailyStats() === $this) {
                $foodConsumed->setDailyStats(null);
            }
        }

        return $this;
    }
}
