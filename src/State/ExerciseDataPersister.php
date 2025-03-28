<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Exercise;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExerciseDataPersister implements ProcessorInterface
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
        if (!$user instanceof User) {
            throw new HttpException(403, "Utilisateur non trouvé.");
        }

        $data->setName(trim($data->getName()));
        if ($data->getDescription()) {
            $data->setDescription(trim($data->getDescription()));
        }
        if ($data->getMuscles()) {
            $musclesArray = array_map('trim', explode(',', $data->getMuscles()));
            $data->setMuscles(implode(', ', array_filter($musclesArray)));
        }

        $data->setCreatedBy($user);
        $data->setStatus(0);

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
