<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Plan\GetUserPlansController;
use App\Repository\PlanRepository;
use App\State\PlanDataPersister;
use App\State\PlanUpdatePersister;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Choice;

#[ORM\Entity(repositoryClass: PlanRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            name: 'get_all_plans'
        ),
        new GetCollection(
            uriTemplate: '/plans/me',
            controller: GetUserPlansController::class,
            normalizationContext: ['groups' => ['plan:read']],
            security: "is_granted('ROLE_USER')",
            read: false,
            name: 'get_user_plans'
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
            validationContext: ['groups' => ['Default', 'plan:create']],
            processor: PlanDataPersister::class,
        ),
        new Patch(
            security: "object.getUser() == user",
            securityMessage: "Tu ne peux modifier que les plans que tu as créés.",
            validationContext: ['groups' => ['Default', 'plan:update']],
            processor: PlanUpdatePersister::class,
        ),
        new Delete(
            security: "object.getUser() == user",
            securityMessage: "Tu ne peux supprimer que les plans que tu as créés.",
            name: 'delete_plan'
        ),
        new Get(
            security: "object.getUser() == user",
            securityMessage: "Tu ne peux voir que les plans que tu as créés.",
            name: 'get_plan'
        )
    ],
    normalizationContext: ['groups' => ['plan:read']],
    denormalizationContext: ['groups' => ['plan:write']],
    security: "is_granted('ROLE_USER')"
)]
class Plan
{
    public const DAYS = [
        'MONDAY' => 'monday',
        'TUESDAY' => 'tuesday',
        'WEDNESDAY' => 'wednesday',
        'THURSDAY' => 'thursday',
        'FRIDAY' => 'friday',
        'SATURDAY' => 'saturday',
        'SUNDAY' => 'sunday',
    ];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['plan:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['plan:read', 'plan:write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['plan:read', 'plan:write'])]
    private ?string $goal = null;

    #[ORM\Column]
    #[Groups(['plan:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'plans')]
    #[Groups(['plan:read'])]
    private ?User $user = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\ManyToMany(targetEntity: Session::class, mappedBy: 'plans', cascade: ['persist', 'remove'])]
    #[Groups(['plan:read', 'plan:write'])]
    private Collection $sessions;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['plan:read', 'plan:write'])]
    #[Choice(
        choices: [
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday'
        ],
        multiple: true
    )]
    private ?array $trainingDays = [];

    #[ORM\Column(nullable: true)]
    #[Groups(['plan:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['plan:read', 'plan:write'])]
    private ?int $duration = null;

    /**
     * @var Collection<int, ScheduledSession>
     */
    #[ORM\OneToMany(targetEntity: ScheduledSession::class, mappedBy: 'plan')]
    private Collection $scheduledSessions; // weeks

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->scheduledSessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGoal(): ?string
    {
        return $this->goal;
    }

    public function setGoal(?string $goal): static
    {
        $this->goal = $goal;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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
            $session->addPlan($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getPlans() === $this) {
                $session->removePlan($this);
            }
        }

        return $this;
    }

    public function getTrainingDays(): array
    {
        return $this->trainingDays ?? [];
    }

    public function setTrainingDays(array $days): static
    {
        $validDays = array_values(self::DAYS);

        foreach ($days as $day) {
            if (!in_array($day, $validDays, true)) {
                throw new \InvalidArgumentException("Jour invalide : $day");
            }
        }

        $this->trainingDays = array_unique($days);
        return $this;
    }

    #[Groups(['plan:read'])]
    public function getFormattedTrainingDays(): array
    {
        $map = [
            'monday' => 'Lundi',
            'tuesday' => 'Mardi',
            'wednesday' => 'Mercredi',
            'thursday' => 'Jeudi',
            'friday' => 'Vendredi',
            'saturday' => 'Samedi',
            'sunday' => 'Dimanche'
        ];

        return array_map(fn($day) => $map[$day] ?? $day, $this->trainingDays);
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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection<int, ScheduledSession>
     */
    public function getScheduledSessions(): Collection
    {
        return $this->scheduledSessions;
    }

    public function addScheduledSession(ScheduledSession $scheduledSession): static
    {
        if (!$this->scheduledSessions->contains($scheduledSession)) {
            $this->scheduledSessions->add($scheduledSession);
            $scheduledSession->setPlan($this);
        }

        return $this;
    }

    public function removeScheduledSession(ScheduledSession $scheduledSession): static
    {
        if ($this->scheduledSessions->removeElement($scheduledSession)) {
            // set the owning side to null (unless already changed)
            if ($scheduledSession->getPlan() === $this) {
                $scheduledSession->setPlan(null);
            }
        }

        return $this;
    }
}
