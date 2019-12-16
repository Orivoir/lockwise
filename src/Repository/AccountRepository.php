<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    // /**
    //  * @return Account[] Returns an array of Account objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Account
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public const PUBLIC_ACCESS  = true;
    public const PRIVATE_ACCESS = false;
    public const LEVEL_UPDATE_SUCCESS = 'success';
    public const LEVEL_UPDATE_WARN = 'warning';
    public const LEVEL_UPDATE_ERROR = 'error';

    public function findAllFavorite( $public = true ) {

        $q = $this->createQueryBuilder('a')
            ->where('a.isFavorite = true')
        ;

        if( $public ) {

            $q->andWhere('a.isRemove = false') ;
        }

        return $q
            ->orderBy('a.createAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllVisible()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isRemove = false')
            ->orderBy('a.createAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;

    }

    public function findAllByUpdateAt( $level = 'success' , $public = true )
    {

        $q = $this->createQueryBuilder('a');

        if( $public )
            $q->where('a.isRemove = false');

        $accounts = $q
            ->orderBy('a.createAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $accountsFilter = [] ;

        if( self::LEVEL_UPDATE_ERROR === $level ) {
            $limit = [20,36500] ;
        } else if( self::LEVEL_UPDATE_WARN === $level ) {
            $limit = [10,20] ;
        } else {
            $limit = [0,10] ;
        }

        foreach( $accounts as $account ) {

            $diffDaysUpdate = $account->getDiffDaysLastUpdate() ;

            if( $diffDaysUpdate > $limit[0] && $diffDaysUpdate < $limit[1] ) {
                $accountsFilter[] = $account ;
            }
        }

        return $accountsFilter ;
    }
}

