<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceReport;
use App\Entity\InvoiceReportError;
use App\Form\InvoiceReportForm;
use App\Message\InvoiceReportMessage;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class InvoiceReportController extends AbstractController
{
    /**
     * @Route("/invoice-report", name="invoice_report_list")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $reports = $em->getRepository(InvoiceReport::class);
        $page = 1;
        // build the query for the doctrine paginator
        $query = $reports->createQueryBuilder('r')
            ->orderBy('r.id', 'DESC')
            ->getQuery();

        $pageSize = '50';
        $paginator = new Paginator($query);

        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $paginator
            ->getQuery()
            ->setFirstResult($pageSize * ($page-1)) // set the offset
            ->setMaxResults($pageSize); // set the limit

        return $this->render('invoice_report/index.html.twig', [
            'totalItems' => $totalItems,
            'pagesCount' => $pagesCount,
            'records' => $paginator
        ]);
    }

    /**
     * @Route("/invoice-report/new", name="invoice_report_new")
     */
    public function new(Request $request, SluggerInterface $slugger)
    {
        $report = new InvoiceReport();
        $form = $this->createForm(InvoiceReportForm::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $reportFile */
            $reportFile = $form->get('report')->getData();

            if ($reportFile) {
                $originalFilename = pathinfo($reportFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$reportFile->guessExtension();

                try {
                    $reportFile->move(
                        $this->getParameter('invoice_reports_dir'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $report->setFileName($this->getParameter('invoice_reports_dir') .'/' .$newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($report);
            $entityManager->flush();
            $this->dispatchMessage(new InvoiceReportMessage($report->getId()));
            return $this->redirectToRoute('invoice_report_list');
        }

        return $this->render('invoice_report/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
