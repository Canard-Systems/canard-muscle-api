<?php

namespace App\Controller\Exercise;

use App\Repository\ExerciseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;

class GetFilteredExercisesController extends AbstractController
{
    private ExerciseRepository $exerciseRepository;
    private Security $security;

    public function __construct(ExerciseRepository $exerciseRepository, Security $security)
    {
        $this->exerciseRepository = $exerciseRepository;
        $this->security = $security;
    }

    public function __invoke(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], Response::HTTP_FORBIDDEN);
        }

        $exercises = $this->exerciseRepository->findFilteredForUser($user);

        return $this->json($exercises, Response::HTTP_OK, [], ['groups' => 'exercise:read']);
    }
}
