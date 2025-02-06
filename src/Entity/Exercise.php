<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Exercise\GetFilteredExercisesController;
use App\Controller\Exercise\ToggleExerciseStatusController;
use App\State\ExerciseDataPersister;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/exercises/filtered',
            controller: GetFilteredExercisesController::class,
            normalizationContext: ['groups' => ['exercise:read']],
            security: "is_granted('ROLE_USER')",
            read: false,
            name: 'get_filtered_exercises'
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            name: 'get_all_exercises'
        ),
        new Post(
            denormalizationContext: ['groups' => ['exercise:write']],
            security: "is_granted('ROLE_USER')",
            processor: ExerciseDataPersister::class
        ),
        new Patch(
            uriTemplate: '/exercises/{id}/toggle-status',
            controller: ToggleExerciseStatusController::class,
            normalizationContext: ['groups' => ['exercise:read']],
            security: "is_granted('ROLE_ADMIN')",
            read: false,
            deserialize: false,
            name: 'toggle_exercise_status'
        ),
        new Delete(
            security: "object.getCreatedBy() == user",
            securityMessage: "Tu ne peux supprimer que les exercices que tu as créés.",
            name: "delete_exercise"
        )
    ],
    security: "is_granted('ROLE_USER')"
)]
class Exercise
{
    #[Groups(['exercise:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[Groups(['exercise:read', 'exercise:write'])]
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Groups(['exercise:read', 'exercise:write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[Groups(['exercise:read', 'exercise:write'])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $muscles = null;

    /**
     * @var Collection<int, SessionExercise>
     */
    #[Groups(['exercise:read'])]
    #[ORM\OneToMany(targetEntity: SessionExercise::class, mappedBy: 'exercise')]
    private Collection $sessionExercises;

    #[Groups(['exercise:read', 'exercise:write'])]
    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $status = null;

    #[Groups(['exercise:read'])]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'exercisesCreated')]
    private ?User $createdBy = null;

    public function __construct()
    {
        $this->sessionExercises = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMuscles(): ?string
    {
        return $this->muscles;
    }

    public function setMuscles(?string $muscles): self
    {
        $this->muscles = $muscles;

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
            $sessionExercise->setExercise($this);
        }
        return $this;
    }

    public function removeSessionExercise(SessionExercise $sessionExercise): static
    {
        if ($this->sessionExercises->removeElement($sessionExercise)) {
            if ($sessionExercise->getExercise() === $this) {
                $sessionExercise->setExercise(null);
            }
        }
        return $this;
    }
    public function getStatus(): ?int { return $this->status; }
    public function setStatus(?int $status): self { $this->status = $status; return $this; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function setCreatedBy(?User $createdBy): self { $this->createdBy = $createdBy; return $this; }
}
