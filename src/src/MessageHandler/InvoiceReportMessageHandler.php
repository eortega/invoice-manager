<?php

namespace App\MessageHandler;

use App\Message\InvoiceReportMessage;
use App\Message\InvoiceMessage;
use App\Repository\InvoiceReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class InvoiceReportMessageHandler implements MessageHandlerInterface
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

    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(
        InvoiceReportRepository $inventoryReportRepository,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        MessageBusInterface $bus)
    {
        $this->inventoryReportRepository = $inventoryReportRepository;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }

    public function __invoke(InvoiceReportMessage $invoiceReportMessage)
    {
        $report = $this->inventoryReportRepository->find($invoiceReportMessage->getInvoiceReportId());

        if(null === $report) {
            $this->logger->critical(
                'Report {id} not found in DB.', ['id' => $invoiceReportMessage->getInvoiceReportId()]
            );
        }

        $this->logger->info('Received to process the invoice report {id}', ['id' => $report->getId()]);

        //1,100,2019-05-20

        $rowIndex = 0;
        if (($handle = fopen($report->getFileName(), "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if(is_array($data) && !empty($data)) {
                    $rowIndex++;
                    $num = count($data);
                    $this->logger->info('Reading the line ' . $rowIndex . ' with cols ' . $num, $data);
                    $this->bus->dispatch(new InvoiceMessage($report->getId(), $rowIndex, $data));
                }
            }

            fclose($handle);

            $report->setRecords($rowIndex);
            $this->entityManager->persist($report);
            $this->entityManager->flush();
        } else {
            //Create an error log
            $this->logger->critical('Impossible to read the Report File  {id}.', ['id' => $report->getId()]);
        }
    }
}
