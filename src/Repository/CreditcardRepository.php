<?php

namespace App\Repository;

use App\Entity\Creditcard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Creditcard|null find($id, $lockMode = null, $lockVersion = null)
 * @method Creditcard|null findOneBy(array $criteria, array $orderBy = null)
 * @method Creditcard[]    findAll()
 * @method Creditcard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditcardRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Creditcard::class);
    }

//    /**
//     * @return Creditcard[] Returns an array of Creditcard objects
//     */
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
    public function findOneBySomeField($value): ?Creditcard
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
