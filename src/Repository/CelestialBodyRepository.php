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

    public function getOneBySlug(string $slug)
    {
        // SELECT 
	    //     celestial_body.*, 
	    //     comment.id AS comment_id
        // FROM 
	    //     celestial_body
        // JOIN 
	    //     comment 
        // ON 
	    //     celestial_body.id = comment.celestial_body_id 
        // WHERE 
	    //     celestial_body.slug = 'earth'
    }

    // /**
    //  * @return CelestialBody[] Returns an array of CelestialBody objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
