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
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

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
                read: false,
                name: 'get_me'
            ),
            new GetCollection(
                security: "is_granted('ROLE_ADMIN')",
                name: 'get_all_users'
            ),
            new Get(
                security: "object == user or is_granted('ROLE_ADMIN')",
                securityMessage: "Tu ne peux voir que ton propre profil.",
                name: 'get_user'
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
    #[Groups(['user:read', 'exercise:read'])]
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
    #[Groups(['user:read'])]
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

    #[Groups(['exercise:read'])]
    public function getApiUrl(): string
    {
        return "/api/users/{$this->id}";
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
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
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
            if ($exercisesCreated->getCreatedBy() === $this) {
                $exercisesCreated->setCreatedBy(null);
            }
        }

        return $this;
    }
}
