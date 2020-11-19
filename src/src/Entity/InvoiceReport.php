<?php

namespace App\Entity;

use App\Repository\InvoiceReportRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=InvoiceReportRepository::class)
 */
class InvoiceReport
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=190)
     */
    private $file_name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $records;

    /**
     * @var DateTime $createdAt
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var DateTime $updatedAt
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=InvoiceReportError::class, mappedBy="invoice_report_id")
     */
    private $invoiceReportErrors;

    /**
     * @ORM\OneToMany(targetEntity=Invoice::class, mappedBy="invoice_report_id")
     */
    private $invoices;

    public function __construct()
    {
        $this->invoiceReportErrors = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    public function setFileName(string $file_name): self
    {
        $this->file_name = $file_name;

        return $this;
    }

    public function getRecords(): ?int
    {
        return $this->records;
    }

    public function setRecords(int $records): self
    {
        $this->records = $records;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection|InvoiceReportError[]
     */
    public function getInvoiceReportErrors(): Collection
    {
        return $this->invoiceReportErrors;
    }

    public function addInvoiceReportError(InvoiceReportError $invoiceReportError): self
    {
        if (!$this->invoiceReportErrors->contains($invoiceReportError)) {
            $this->invoiceReportErrors[] = $invoiceReportError;
            $invoiceReportError->setInvoiceReportId($this);
        }

        return $this;
    }

    public function removeInvoiceReportError(InvoiceReportError $invoiceReportError): self
    {
        if ($this->invoiceReportErrors->removeElement($invoiceReportError)) {
            // set the owning side to null (unless already changed)
            if ($invoiceReportError->getInvoiceReportId() === $this) {
                $invoiceReportError->setInvoiceReportId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setInvoiceReportId($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getInvoiceReportId() === $this) {
                $invoice->setInvoiceReportId(null);
            }
        }

        return $this;
    }
}
