<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Plan;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PlanUpdatePersister implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Plan
    {
        $data->setUpdatedAt((new \DateTimeImmutable())->setTimezone(new \DateTimeZone('Europe/Paris')));
        $data->setTrainingDays($data->getTrainingDays());
        $data->setGoal($data->getGoal());
        $data->setName($data->getName());
        $data->setDuration($data->getDuration());
        $this->entityManager->flush();

        return $data;
    }

}
