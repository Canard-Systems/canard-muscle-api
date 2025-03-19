<?php

namespace App\Controller\Session;

use App\Repository\SessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GetUserSessionsController extends AbstractController
{
    private SessionRepository $sessionRepository;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function __invoke(Request $request): iterable
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException('Utilisateur non authentifié.');
        }
        return $this->sessionRepository->findBy(['user' => $user]);
    }
}
