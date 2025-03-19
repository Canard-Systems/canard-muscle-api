<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Session\GetUserSessionsController;
use App\Repository\SessionRepository;
use App\State\SessionDataPersister;
use App\State\SessionUpdatePersister;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            name: 'get_all_sessions'
        ),
        new GetCollection(
            uriTemplate: '/sessions/me',
            controller: GetUserSessionsController::class,
            normalizationContext: ['groups' => ['session:read']],
            security: "is_granted('ROLE_USER')",
            read: false,
            name: 'get_user_sessions'
        ),
        new Post(
            denormalizationContext: ['groups' => ['session:write']],
            security: "is_granted('ROLE_USER')",
            processor: SessionDataPersister::class,
        ),
        new Patch(
            security: "object.getUser() == user",
            validationContext: ['groups' => ['session:write']],
            processor: SessionUpdatePersister::class,
        ),
        new Delete(
            security: "object.getUser() == user",
            name: 'delete_session'
        ),
        new Get(
            security: "object.getUser() == user",
            securityMessage: "Tu ne peux voir que les sessions que tu as créées.",
            name: 'get_session'
        )
    ],
    normalizationContext: ['groups' => ['session:read']],
    denormalizationContext: ['groups' => ['session:write']],
    security: "is_granted('ROLE_USER')"
)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['session:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['session:read', 'session:write'])]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['session:read', 'session:write'])]
    private ?int $duration = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['session:read', 'session:write'])]
    private ?int $distance = null;

    /**
     * @var Collection<int, Plan>
     */
    #[ORM\ManyToMany(targetEntity: Plan::class, inversedBy: 'sessions', cascade: ['persist', 'remove'])]
    #[Groups(['session:read', 'session:write', 'plan:read'])]
    private Collection $plans;

    /**
     * @var Collection<int, SessionExercise>
     */
    #[ORM\OneToMany(targetEntity: SessionExercise::class, mappedBy: 'session', cascade: ['persist', 'remove'])]
    #[Groups(['session:read', 'session:write'])]
    private Collection $sessionExercises;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups(['session:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['session:read', 'session:write'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->plans = new ArrayCollection();
        $this->sessionExercises = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;
        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(?int $distance): static
    {
        $this->distance = $distance;
        return $this;
    }

    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function addPlan(Plan $plan): static
    {
        if (!$this->plans->contains($plan)) {
            $this->plans->add($plan);
            $plan->addSession($this); // Assure la synchronisation bidirectionnelle
        }
        return $this;
    }

    public function removePlan(Plan $plan): static
    {
        if ($this->plans->removeElement($plan)) {
            $plan->removeSession($this); // Assure la synchronisation bidirectionnelle
        }
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, SessionExercise>
     */
    public function getSessionExercises(): Collection
    {
        return $this->sessionExercises;
    }

    public function addSessionExercise(SessionExercise $sessionExercise): static
    {
        if (!$this->sessionExercises->contains($sessionExercise)) {
            $this->sessionExercises->add($sessionExercise);
            $sessionExercise->setSession($this);
        }
        return $this;
    }

    public function removeSessionExercise(SessionExercise $sessionExercise): static
    {
        if ($this->sessionExercises->removeElement($sessionExercise)) {
            if ($sessionExercise->getSession() === $this) {
                $sessionExercise->setSession(null);
            }
        }
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
