<?php

namespace App\Repository;

use App\Entity\InvoiceReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InvoiceReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceReport[]    findAll()
 * @method InvoiceReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceReport::class);
    }

    // /**
    //  * @return InvoiceReport[] Returns an array of InvoiceReport objects
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
    public function findOneBySomeField($value): ?InvoiceReport
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
