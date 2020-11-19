<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 */
class Invoice
{

    const MULTIPLIER = 100;

    const MIN_COEFFICIENT = 0.3;

    const MAX_COEFFICIENT = 0.5;

    const DAYS_TO_DUE = 30;

    const DUE_DATE_FORMAT = 'Y-m-d';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $number;

    /**
     * @ORM\Column(type="decimal", precision=19, scale=4)
     */
    private $amount;

    /**
     * @ORM\Column(type="decimal", precision=19, scale=4)
     */
    private $selling_price;

    /**
     * @var string A "Y-m-d" formatted value
     * @ORM\Column(type="date")
     */
    private $due_on;

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
     * @ORM\ManyToOne(targetEntity=InvoiceReport::class, inversedBy="invoices")
     */
    private $invoice_report_id;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getAmount(): ? float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSellingPrice(): ? float
    {
        return $this->selling_price;
    }

    public function setSellingPrice(float $selling_price): self
    {
        $this->selling_price = $selling_price;

        return $this;
    }

    public function getDueOn(): ?\DateTimeInterface
    {
        return $this->due_on;
    }

    public function setDueOn(\DateTimeInterface $due_on): self
    {
        $this->due_on = $due_on;

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

    public function getInvoiceReportId(): ?InvoiceReport
    {
        return $this->invoice_report_id;
    }


    public function setInvoiceReportId(?InvoiceReport $invoice_report_id): self
    {
        $this->invoice_report_id = $invoice_report_id;

        return $this;
    }

    /**
     * Invoice sell price depends on amount and days to the due date.
     * - The formula is amount * coefficient.

     * @return int
     */
    public function calculateSellingPrice(): float
    {
        return $this->getAmount() * $this->calculateSellingPriceCoefficient();
    }

    /**
     * - The coefficient is 0.5 when the invoice uploaded more than 30 days before? the due date
     * - The coefficient is 0.3 when less or equal to 30 days.
     * @return float
     */
    public function calculateSellingPriceCoefficient(): float
    {
        $currentDate = Carbon::today();
        $dueDate = Carbon::createFromFormat('Y-m-d', $this->due_on->format('Y-m-d'));
        $remainingDays = $currentDate->diffInDays($dueDate, false);
        return $remainingDays <= self::DAYS_TO_DUE ? self::MIN_COEFFICIENT : self::MAX_COEFFICIENT;
    }
}
