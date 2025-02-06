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

        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $this->security->getUser()->getUserIdentifier()]);
        if (!$user) {
            throw new HttpException(403, "Utilisateur non trouvé. {$this->security->getUser()->getUserIdentifier()}");
        }

        $data->setName(mb_strtolower(trim($data->getName())));
        if ($data->getDescription()) {
            $data->setDescription(mb_strtolower(trim($data->getDescription())));
        }
        if ($data->getMuscles()) {
            $musclesArray = array_filter(array_map('trim', explode(',', $data->getMuscles())), function($value) {
                return $value !== '';
            });
            $cleanedMuscles = implode(',', $musclesArray);
            $data->setMuscles(mb_strtolower($cleanedMuscles));
        }

        $data->setCreatedBy($user);
        $data->setStatus(0);

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
