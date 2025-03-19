<?php

namespace App\State;


use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Session;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SessionDataPersister implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Session
    {
        if (!$data instanceof Session) {
            throw new HttpException(400, "Données invalides.");
        }
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $this->security->getUser()->getUserIdentifier()]);
        if (!$user) {
            throw new HttpException(403, "Utilisateur non trouvé. {$this->security->getUser()->getUserIdentifier()}");
        }

        $data->setName('Nouvelle séance');
        $data->setDuration(0);
        $data->setDistance(0);
        $data->setUser($user);
        $data->setCreatedAt((new \DateTimeImmutable())->setTimezone(new \DateTimeZone('Europe/Paris')));
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        return $data;
    }
}
