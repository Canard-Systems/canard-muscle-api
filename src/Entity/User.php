<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\User\GetUserMeController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[
    ApiResource(
        operations: [
            new Get(
                uriTemplate: '/me',
                controller: GetUserMeController::class,
                normalizationContext: ['groups' => ['user:read']],
                security: "is_granted('ROLE_USER')",
                securityMessage: "Tu ne peux voir que tes informations.",
                read: false, // Désactive le chargement automatique de l'entité
                name: 'get_me',
            ),
            new GetCollection(
                security: "is_granted('ROLE_ADMIN')",
                name: 'get_all_users'
            ),
            new Post(),
            new Patch(),
            new Delete(),

        ],
        forceEager: false
    )
]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['user:read'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?int $age = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?int $weight = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?int $height = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read'])]
    private ?string $gender = null;

    /**
     * @var Collection<int, Plan>
     */
    #[ORM\OneToMany(targetEntity: Plan::class, mappedBy: 'user')]
    #[Groups(['user:read'])]
    #[MaxDepth(1)]
    private Collection $plans;

    /**
     * @var Collection<int, UserSetting>
     */
    #[ORM\OneToMany(targetEntity: UserSetting::class, mappedBy: 'user')]
    private Collection $userSettings;

    /**
     * @var Collection<int, Exercise>
     */
    #[ORM\OneToMany(targetEntity: Exercise::class, mappedBy: 'createdBy')]
    private Collection $exercisesCreated;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\OneToMany(targetEntity: Session::class, mappedBy: 'user')]
    private Collection $sessions;

    /**
     * @var Collection<int, ApiToken>
     */
    #[ORM\OneToMany(targetEntity: ApiToken::class, mappedBy: 'user')]
    private Collection $apiTokens;

    public function __construct()
    {
        $this->plans = new ArrayCollection();
        $this->userSettings = new ArrayCollection();
        $this->exercisesCreated = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->apiTokens = new ArrayCollection();
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

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return Collection<int, Plan>
     */
    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function addPlan(Plan $plan): static
    {
        if (!$this->plans->contains($plan)) {
            $this->plans->add($plan);
            $plan->setUser($this);
        }

        return $this;
    }

    public function removePlan(Plan $plan): static
    {
        if ($this->plans->removeElement($plan)) {
            // set the owning side to null (unless already changed)
            if ($plan->getUser() === $this) {
                $plan->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserSetting>
     */
    public function getUserSettings(): Collection
    {
        return $this->userSettings;
    }

    public function addUserSetting(UserSetting $userSetting): static
    {
        if (!$this->userSettings->contains($userSetting)) {
            $this->userSettings->add($userSetting);
            $userSetting->setUser($this);
        }

        return $this;
    }

    public function removeUserSetting(UserSetting $userSetting): static
    {
        if ($this->userSettings->removeElement($userSetting)) {
            // set the owning side to null (unless already changed)
            if ($userSetting->getUser() === $this) {
                $userSetting->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Exercise>
     */
    public function getExercisesCreated(): Collection
    {
        return $this->exercisesCreated;
    }

    public function addExercisesCreated(Exercise $exercisesCreated): static
    {
        if (!$this->exercisesCreated->contains($exercisesCreated)) {
            $this->exercisesCreated->add($exercisesCreated);
            $exercisesCreated->setCreatedBy($this);
        }

        return $this;
    }

    public function removeExercisesCreated(Exercise $exercisesCreated): static
    {
        if ($this->exercisesCreated->removeElement($exercisesCreated)) {
            // set the owning side to null (unless already changed)
            if ($exercisesCreated->getCreatedBy() === $this) {
                $exercisesCreated->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setUser($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getUser() === $this) {
                $session->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): static
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
            $apiToken->setUser($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): static
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getUser() === $this) {
                $apiToken->setUser(null);
            }
        }

        return $this;
    }
}
