<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Exercise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExerciseUpdatePersister implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Exercise
    {
        if (!$data instanceof Exercise) {
            throw new HttpException(400, "Données invalides.");
        }

        $user = $this->security->getUser();
        if (!$user || $data->getCreatedBy()->getId() !== $user->getId()) {
            throw new HttpException(403, "Vous n'êtes pas autorisé à modifier cet exercice.");
        }

        // Mise à jour des champs
        $data->setName(trim($data->getName()));
        if ($data->getDescription()) {
            $data->setDescription(trim($data->getDescription()));
        }
        if ($data->getMuscles()) {
            $musclesArray = array_map('trim', explode(',', $data->getMuscles()));
            $data->setMuscles(implode(', ', array_filter($musclesArray)));
        }

        $this->entityManager->flush();

        return $data;
    }
}
