<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Plan;
use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionUpdatePersister implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;
    private RequestStack $requestStack;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Session
    {
        if (!$data instanceof Session) {
            throw new \InvalidArgumentException("Données invalides.");
        }

        $request = $this->requestStack->getCurrentRequest();
        $requestData = json_decode($request->getContent(), true);

        $data->setName($requestData['name'] ?? $data->getName());
        $data->setDuration($requestData['duration'] ?? $data->getDuration());
        $data->setDistance($requestData['distance'] ?? $data->getDistance());

        $data->getPlans()->clear();
        if (!empty($requestData['plans'])) {
            foreach ($requestData['plans'] as $planIri) {
                if (preg_match('/\/api\/plans\/(\d+)/', $planIri, $matches)) {
                    $planId = (int) $matches[1];
                    $plan = $this->entityManager->getRepository(Plan::class)->find($planId);
                    if ($plan) {
                        $data->addPlan($plan);
                    }
                }
            }
        }

        $data->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
