<?php

namespace App\Controller\Plan;

use App\Repository\PlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GetUserPlansController extends AbstractController
{
    private PlanRepository $planRepository;

    public function __construct(PlanRepository $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    public function __invoke(Request $request): iterable
    {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException('Utilisateur non authentifié.');
        }
        return $this->planRepository->findBy(['user' => $user]);
    }
}
