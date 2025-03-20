<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\SessionExerciseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;

#[ORM\Entity(repositoryClass: SessionExerciseRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER')"
        ),
        new Get(
            security: "is_granted('ROLE_USER')"
        ),
        new Post(
            denormalizationContext: ['groups' => ['session_exercise:write']],
            security: "is_granted('ROLE_USER')"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['session_exercise:write']],
            security: "is_granted('ROLE_USER')"
        ),
        new Delete(
            security: "is_granted('ROLE_USER')"
        ),
    ],
    normalizationContext: ['groups' => ['session_exercise:read']],
    denormalizationContext: ['groups' => ['session_exercise:write']],
    security: "is_granted('ROLE_USER')"
)]
class SessionExercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['session_exercise:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sessionExercises')]
    #[Groups(['session_exercise:read', 'session_exercise:write'])]
    #[MaxDepth(1)]
    private ?Session $session = null;

    #[ORM\ManyToOne(inversedBy: 'sessionExercises')]
    #[Groups(['session_exercise:read', 'session_exercise:write'])]
    #[MaxDepth(1)]
    private ?Exercise $exercise = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['session_exercise:read', 'session_exercise:write'])]
    private ?int $sets = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['session_exercise:read', 'session_exercise:write'])]
    private ?array $repsPerSet = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['session_exercise:read', 'session_exercise:write'])]
    private ?int $weight = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['session_exercise:read', 'session_exercise:write'])]
    private ?int $duration = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getExercise(): ?Exercise
    {
        return $this->exercise;
    }

    public function setExercise(?Exercise $exercise): static
    {
        $this->exercise = $exercise;
        return $this;
    }

    public function getSets(): ?int
    {
        return $this->sets;
    }

    public function setSets(?int $sets): static
    {
        $this->sets = $sets;
        return $this;
    }

    public function getRepsPerSet(): ?array
    {
        return $this->repsPerSet;
    }

    public function setRepsPerSet(?array $repsPerSet): static
    {
        $this->repsPerSet = $repsPerSet;
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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;
        return $this;
    }
}
