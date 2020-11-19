<?php


namespace App\Message;


class InvoiceReportMessage
{
    private $invoiceReportId;

    public function __construct(int $invoiceReportId)
    {
        $this->invoiceReportId = $invoiceReportId;
    }

    public function getInvoiceReportId(): int
    {
        return $this->invoiceReportId;
    }
}
