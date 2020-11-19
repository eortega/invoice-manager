<?php

namespace App\Repository;

use App\Entity\InvoiceReportError;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InvoiceReportError|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceReportError|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceReportError[]    findAll()
 * @method InvoiceReportError[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceReportErrorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceReportError::class);
    }

    // /**
    //  * @return InvoiceReportError[] Returns an array of InvoiceReportError objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InvoiceReportError
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
