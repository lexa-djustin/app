<?php

namespace App\Controller\Helper;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity;
use App\Controller\Exception;

class User
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * User constructor.
     *
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->entityManager = $entityManager;
    }

    /**
     * @return Entity\User
     * @throws Exception\UserWasNotFound
     */
    public function checkAndGet()
    {
        /** @var Entity\User $user */
        $user = $this->entityManager->getRepository(Entity\User::class)->findOneByToken(
            $this->request->headers->get('x-access-token')
        );

        if (!$user) {
            throw new Exception\UserWasNotFound('Unauthorized', 401);
        }

        $this->entityManager->refresh($user);

        return $user;
    }
}