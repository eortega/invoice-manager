<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceReport;
use App\Entity\InvoiceReportError;
use App\Form\InvoiceReportForm;
use App\Message\InvoiceReportMessage;
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
        return $this->render('invoice_report/index.html.twig', [
            'controller_name' => 'InvoiceReportController',
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

    /**
     * @Route("/invoice-report/test", name="invoice_report_new_test")
     */
    public function createProduct(): Response
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createProduct(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $report = new InvoiceReport();
        $report->setFileName('november');
        $report->setRecords(19);



        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($report);
        $reportError = new InvoiceReportError();
        $reportError->setInvoiceReportId($report);
        $reportError->setData("{}");
        $reportError->setLine(2312);
        $reportError->setError('Empty_Data');

        $invoice = new Invoice();
        $invoice->setNumber('A-24');
        $invoice->setAmount(1255);
        $invoice->setSellingPrice(1355);
        $invoice->setDueOn(new \DateTime('2019-01-09'));
        $invoice->setInvoiceReportId($report);

        $entityManager->persist($reportError);
        $entityManager->persist($invoice);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new Invoice Report with id '.$report->getId());
    }

}
