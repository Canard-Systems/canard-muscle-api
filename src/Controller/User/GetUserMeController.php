<?php

namespace App\Controller\User;

use App\Repository\ExerciseRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GetUserMeController extends AbstractController
{
    public function __invoke(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }
}
