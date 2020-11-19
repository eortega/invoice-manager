<?php

namespace App\Controller;

use App\Entity\Invoice;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    /**
     * @Route("/invoice-per-report/{reportId}", name="invoice_list_by_report")
     */
    public function invoicesPerReport($reportId): Response
    {
        $em = $this->getDoctrine()->getManager();
        $invoices = $em->getRepository(Invoice::class);
        $page = 1;

        $query = $invoices->createQueryBuilder('i')
            ->where('i.invoice_report_id = ?1')
            ->orderBy('i.createdAt', 'DESC')
            ->setParameter(1, $reportId)
            ->getQuery();

        $pageSize = '50';
        $paginator = new Paginator($query);

        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page-1)) // set the offset
            ->setMaxResults($pageSize); // set the limit

        return $this->render('invoice/invoices-per-report.html.twig', [
            'report_id' => $reportId,
            'totalItems' => $totalItems,
            'pagesCount' => $pagesCount,
            'records' => $paginator
        ]);
    }
}
