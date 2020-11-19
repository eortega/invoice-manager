<?php

namespace App\Entity;

use App\Repository\InvoiceReportErrorRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=InvoiceReportErrorRepository::class)
 */
class InvoiceReportError
{

    public const ERROR_TOO_FEW_PARAMETERS = 'TOO_FEW_PARAMETERS';

    public const ERROR_INVALID_DUE_DATE = 'INVALID_DUE_DATE';

    public const ERROR_INVOICE_NUMBER_DUPLICATED = 'INVOICE_NUMBER_DUPLICATED';

    public const ERROR_INVALID_AMOUNT = 'INVALID_AMOUNT';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $data;

    /**
     * @ORM\Column(type="integer")
     */
    private $line;

    /**
     * @ORM\Column(type="string", length=190)
     */
    private $error;

    /**
     * @var DateTime $createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=InvoiceReport::class, inversedBy="invoiceReportErrors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $invoice_report_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getLine(): ?int
    {
        return $this->line;
    }

    public function setLine(int $line): self
    {
        $this->line = $line;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(string $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getInvoiceReportId(): ?InvoiceReport
    {
        return $this->invoice_report_id;
    }

    public function setInvoiceReportId(?InvoiceReport $invoice_report_id): self
    {
        $this->invoice_report_id = $invoice_report_id;

        return $this;
    }
}
