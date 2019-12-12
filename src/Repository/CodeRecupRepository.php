<?php

namespace App\Repository;

use App\Entity\CodeRecup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CodeRecup|null find($id, $lockMode = null, $lockVersion = null)
 * @method CodeRecup|null findOneBy(array $criteria, array $orderBy = null)
 * @method CodeRecup[]    findAll()
 * @method CodeRecup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeRecupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodeRecup::class);
    }

    // /**
    //  * @return CodeRecup[] Returns an array of CodeRecup objects
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

    /*
    public function findOneBySomeField($value): ?CodeRecup
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
