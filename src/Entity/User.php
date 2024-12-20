<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, UserData>
     */
    #[ORM\OneToMany(targetEntity: UserData::class, mappedBy: 'user')]
    private Collection $userData;

    /**
     * @var Collection<int, FoodConsumed>
     */
    #[ORM\OneToMany(targetEntity: FoodConsumed::class, mappedBy: 'user')]
    private Collection $foodConsumeds;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $activationToken = null;

    public function __construct()
    {
        $this->userData = new ArrayCollection();
        $this->foodConsumeds = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    /**
     * @return Collection<int, UserData>
     */
    public function getUserData(): Collection
    {
        return $this->userData;
    }

    public function addUserData(UserData $userData): static
    {
        if (!$this->userData->contains($userData)) {
            $this->userData->add($userData);
            $userData->setUser($this);
        }

        return $this;
    }

    public function removeUserData(UserData $userData): static
    {
        if ($this->userData->removeElement($userData)) {
            // set the owning side to null (unless already changed)
            if ($userData->getUser() === $this) {
                $userData->setUser(null);
            }
        }

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
            $foodConsumed->setUser($this);
        }

        return $this;
    }

    public function removeFoodConsumed(FoodConsumed $foodConsumed): static
    {
        if ($this->foodConsumeds->removeElement($foodConsumed)) {
            // set the owning side to null (unless already changed)
            if ($foodConsumed->getUser() === $this) {
                $foodConsumed->setUser(null);
            }
        }

        return $this;
    }

    public function getIsVerified(): bool
{
    return $this->isVerified;
}

public function setIsVerified(bool $isVerified): self
{
    $this->isVerified = $isVerified;
    return $this;
}
public function getActivationToken(): ?string
{
    return $this->activationToken;
}

public function setActivationToken(?string $activationToken): self
{
    $this->activationToken = $activationToken;
    return $this;
}
}
