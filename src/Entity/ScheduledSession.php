<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ScheduledSessionRepository;
use App\State\ScheduledSessionDataPersister;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ScheduledSessionRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            name: 'get_all_scheduled_sessions'
        ),
        // Récupérer les programmations de l’utilisateur
        new GetCollection(
            uriTemplate: '/scheduled_sessions/me',
            security: "is_granted('ROLE_USER')",
            name: 'get_user_scheduled_sessions',
        // un contrôleur custom si tu veux filtrer par user, ou un filtre via la config
        ),
        // Créer une programmation
        new Post(
            denormalizationContext: ['groups' => ['scheduled_session:write']],
            security: "is_granted('ROLE_USER')",
            processor: ScheduledSessionDataPersister::class,
        ),
        // Modifier
        new Patch(
            denormalizationContext: ['groups' => ['scheduled_session:write']],  // l’utilisateur ne modifie que ses propres programmations
            security: "object.getUser() == user",
        ),
        // Supprimer
        new Delete(
            security: "object.getUser() == user"
        ),
        // Obtenir une programmation
        new Get(
            security: "object.getUser() == user",
            name: 'get_scheduled_session'
        )
    ],
    normalizationContext: ['groups' => ['scheduled_session:read']],
    denormalizationContext: ['groups' => ['scheduled_session:write']],
    security: "is_granted('ROLE_USER')"
)]
class ScheduledSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['scheduled_session:read'])]
    private ?int $id = null;

    // La date programmée (jour/heure). DateTimeImmutable recommandé
    #[ORM\Column]
    #[Groups(['scheduled_session:read', 'scheduled_session:write'])]
    private ?\DateTimeImmutable $scheduledAt = null;

    // Liaison à la Session « modèle »
    #[ORM\ManyToOne(targetEntity: Session::class)]
    #[Groups(['scheduled_session:read', 'scheduled_session:write'])]
    private ?Session $session = null;

    // Liaison optionnelle au Plan, si on veut associer la prog à un plan
    #[ORM\ManyToOne(targetEntity: Plan::class)]
    #[Groups(['scheduled_session:read', 'scheduled_session:write'])]
    private ?Plan $plan = null;

    // L’utilisateur qui programme la séance
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['scheduled_session:read'])]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups(['scheduled_session:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['scheduled_session:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScheduledAt(): ?\DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(\DateTimeImmutable $scheduledAt): static
    {
        $this->scheduledAt = $scheduledAt;
        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;
        return $this;
    }

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): static
    {
        $this->plan = $plan;
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
