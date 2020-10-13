<?php

namespace App\Repository;

use App\Entity\VAT;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method VAT|null find($id, $lockMode = null, $lockVersion = null)
 * @method VAT|null findOneBy(array $criteria, array $orderBy = null)
 * @method VAT[]    findAll()
 * @method VAT[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VATRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VAT::class);
    }

    // /**
    //  * @return VAT[] Returns an array of VAT objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VAT
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
