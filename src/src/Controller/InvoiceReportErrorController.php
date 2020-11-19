<?php

namespace App\Controller;

use App\Entity\InvoiceReport;
use App\Entity\InvoiceReportError;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceReportErrorController extends AbstractController
{
    /**
     * @Route("/invoice-report-error/{reportId}", name="invoice_report_error")
     */
    public function index($reportId): Response
    {
        $em = $this->getDoctrine()->getManager();
        $reports = $em->getRepository(InvoiceReportError::class);
        $page = 1;

        $query = $reports->createQueryBuilder('e')
            ->where('e.invoice_report_id = ?1')
            ->orderBy('e.line', 'ASC')
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

        return $this->render('invoice_report_error/index.html.twig', [
            'report_id' => $reportId,
            'totalItems' => $totalItems,
            'pagesCount' => $pagesCount,
            'records' => $paginator
        ]);
    }
}
