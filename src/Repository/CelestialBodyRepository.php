<?php

namespace App\Repository;

use App\Entity\CelestialBody;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CelestialBody|null find($id, $lockMode = null, $lockVersion = null)
 * @method CelestialBody|null findOneBy(array $criteria, array $orderBy = null)
 * @method CelestialBody[]    findAll()
 * @method CelestialBody[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CelestialBodyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CelestialBody::class);
    }
}
