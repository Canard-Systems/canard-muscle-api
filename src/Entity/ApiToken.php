<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use App\Service\EncryptionService;
use Doctrine\ORM\Mapping as ORM;
use Random\RandomException;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
class ApiToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $encryptedToken = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private ?string $tokenHash = null;
    #[ORM\ManyToOne(inversedBy: 'apiTokens')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;

    private ?EncryptionService $encryptionService = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setEncryptionService(EncryptionService $encryptionService): self
    {
        $this->encryptionService = $encryptionService;
        return $this;
    }

    public function setToken(string $token): self
    {
        if (!$this->encryptionService) {
            throw new \LogicException('EncryptionService non défini.');
        }

        $this->encryptedToken = $this->encryptionService->encrypt($token);
        $this->tokenHash = hash('sha256', $token);

        return $this;
    }

    public function getToken(): ?string
    {
        if (!$this->encryptionService) {
            throw new \LogicException('EncryptionService non défini.');
        }

        return $this->encryptedToken
            ? $this->encryptionService->decrypt($this->encryptedToken)
            : null;
    }

    public function getEncryptedToken(): ?string
    {
        return $this->encryptedToken;
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
}
