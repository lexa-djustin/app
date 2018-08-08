<?php

namespace App\Repository;

use App\Entity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class User extends ServiceEntityRepository
{
    /**
     * User constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entity\User::class);
    }

    /**
     * @param string $token
     *
     * @return Entity\User
     */
    public function findOneByToken($token)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('u')
            ->from(Entity\User::class, 'u')
            ->join('u.token', 't')
            ->andWhere('t.token = :token');

        $qb->setParameter('token', $token);

        return $qb->getQuery()->getOneOrNullResult();
    }
}