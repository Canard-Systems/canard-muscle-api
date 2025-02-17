<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ApiToken;
use App\Repository\UserRepository;

class JWTAuthenticationSuccessListener
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function __invoke(AuthenticationSuccessEvent $event): void
    {
        // Récupérer le token JWT généré
        $data = $event->getData();
        if (!isset($data['token'])) {
            die();
        }
        $jwt = $data['token'];

        // Récupérer l'utilisateur
        $user = $this->userRepository->findOneBy(['email' => $event->getUser()->getUserIdentifier()]);
        if (!$user) {
            die();
        }


        // Supprimer les anciens tokens de l'utilisateur
        $this->entityManager->createQuery(
            'DELETE FROM App\Entity\ApiToken t WHERE t.user = :user'
        )->setParameter('user', $user)->execute();

        // Sauvegarder le nouveau token
        $apiToken = new ApiToken();
        $apiToken->setToken($jwt);
        $apiToken->setUser($user);

        $this->entityManager->persist($apiToken);
        $this->entityManager->flush();

    }
}
