<?php

namespace App\Controller\Exercise;

use App\Entity\Exercise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ToggleExerciseStatusController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Exercise $data): Exercise
    {
        $currentStatus = $data->getStatus() ?? 0;
        $newStatus = ($currentStatus === 0) ? 1 : 0;
        $data->setStatus($newStatus);
        $this->entityManager->flush();

        return $data;
    }
}
