<?php

namespace App\MessageHandler;

use App\Entity\InvoiceReportError;
use App\Entity\Invoice;
use App\Message\InvoiceMessage;
use App\Entity\InvoiceReport;
use App\Repository\InvoiceReportRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class InvoiceMessageHandler implements MessageHandlerInterface
{

    /**
     * @var InvoiceReportRepository
     */
    private $inventoryReportRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        InvoiceReportRepository $inventoryReportRepository,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager)
    {
        $this->inventoryReportRepository = $inventoryReportRepository;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function __invoke(InvoiceMessage $invoiceMessage)
    {
        $report = $this->inventoryReportRepository->find($invoiceMessage->getInvoiceReportId());

        if(null === $report) {
            $this->logger->critical(
                'Report {id} not found in DB.', ['id' => $invoiceMessage->getInvoiceReportId()]
            );
        }

        $this->logger->info('Invoice incoming to process {id}', ['id' => $report->getId()]);
        $data = $invoiceMessage->getData();
        $filteredData = array_filter($data);

        if (3 !== count($filteredData)) {
            $this->storeError($report, $invoiceMessage, InvoiceReportError::ERROR_TOO_FEW_PARAMETERS);
            return;
        }

        [$number, $amount, $due_date] = $data;

        $invoicesRepo = $this->entityManager->getRepository("App:Invoice");
        $invoiceExists = $invoicesRepo->findBy(['number' => $number]);

        if ($invoiceExists) {
            $this->logger->info('Another invoices with the same number already exists', ['number' => $number]);
            $this->storeError($report, $invoiceMessage, InvoiceReportError::ERROR_INVOICE_NUMBER_DUPLICATED);
            return;
        }

        if (!is_numeric($amount)) {
            $this->logger->info('Invalid amount provided', ['amount' => $amount]);
            $this->storeError($report, $invoiceMessage, InvoiceReportError::ERROR_INVALID_AMOUNT);
            return;
        }

        if (!$this->isValidateDate($due_date)) {
            $this->logger->error(InvoiceReportError::ERROR_INVALID_DUE_DATE);
            $this->storeError($report, $invoiceMessage, InvoiceReportError::ERROR_INVALID_DUE_DATE);
            return;
        }

        $invoice = new Invoice();
        $invoice->setNumber($number);
        $invoice->setInvoiceReportId($report);
        $invoice->setAmount($amount);
        $invoice->setDueOn(\DateTime::createFromFormat(Invoice::DUE_DATE_FORMAT, $due_date));
        $invoice->setSellingPrice($invoice->calculateSellingPrice());
        $this->entityManager->persist($invoice);
        $this->entityManager->flush();
    }

    /**
     * @param string $date
     * @return bool
     */
    public function isValidateDate(string $date): bool
    {
        $d = \DateTime::createFromFormat(Invoice::DUE_DATE_FORMAT, $date);
        return $d && $d->format(Invoice::DUE_DATE_FORMAT) === $date;
    }

    protected function storeError(InvoiceReport $report, InvoiceMessage $invoiceMessage, string $error): void
    {
        $errorLog = new InvoiceReportError();
        $errorLog->setInvoiceReportId($report);
        $errorLog->setError($error);
        $errorLog->setData(implode(',', $invoiceMessage->getData()));
        $errorLog->setLine($invoiceMessage->getRowNumber());
        $this->entityManager->persist($errorLog);
        $this->entityManager->flush();
    }
}
