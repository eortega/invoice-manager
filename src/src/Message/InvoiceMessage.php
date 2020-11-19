<?php


namespace App\Message;


class InvoiceMessage
{
    private $invoiceReportId;

    private $rowNumber;

    private $data;

    public function __construct(int $invoiceReportId, int $rowNumber, array $data)
    {
        $this->invoiceReportId = $invoiceReportId;
        $this->rowNumber =$rowNumber;
        $this->data = $data;
    }

    public function getInvoiceReportId(): int
    {
        return $this->invoiceReportId;
    }

    public function getRowNumber(): int
    {
        return $this->rowNumber;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
