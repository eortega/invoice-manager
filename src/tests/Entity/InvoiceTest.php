<?php

namespace App\Tests\Entity;

use App\Entity\Invoice;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class InvoiceTest extends TestCase
{

    public function testCalculateSellingPriceCoefficient(): void
    {
        //Due date is in past
        $knownDate = Carbon::create(2020, 11, 1);
        Carbon::setTestNow($knownDate);
        $invoice = new Invoice();
        $invoice->setDueOn(new \DateTime('2020-10-31'));
        $coeff = $invoice->calculateSellingPriceCoefficient();
        $this->assertSame(0.3, $coeff, "Due date in past failed");


        //Due date is in past
        $knownDate = Carbon::create(2020, 12, 1);
        Carbon::setTestNow($knownDate);
        $invoice = new Invoice();
        $invoice->setDueOn(new \DateTime('2020-12-31'));
        $coeff = $invoice->calculateSellingPriceCoefficient();
        $this->assertSame(0.3, $coeff, "Due date in due day failed");


        //Due date is in past
        $knownDate = Carbon::create(2020, 12, 1);
        Carbon::setTestNow($knownDate);
        $invoice = new Invoice();
        $invoice->setDueOn(new \DateTime('2021-1-1'));
        $coeff = $invoice->calculateSellingPriceCoefficient();
        $this->assertSame(0.5, $coeff, "Due date after the due day failed");

        //Due date will take place in more than 30 days
        $knownDate = Carbon::create(2020, 11, 15);
        Carbon::setTestNow($knownDate);
        $invoice = new Invoice();
        $invoice->setDueOn(new \DateTime('2020-12-31'));
        $coeff = $invoice->calculateSellingPriceCoefficient();
        $this->assertSame(0.5, $coeff, "Due date in more than 30 days failed");
    }

    public function testCalculateSellingPrice(): void
    {
        //Due date is in past
        $knownDate = Carbon::create(2020, 11, 1);
        Carbon::setTestNow($knownDate);
        $invoice = new Invoice();
        $invoice->setDueOn(new \DateTime('2020-10-31'));
        $invoice->setAmount(1000);
        $sellingPrice = $invoice->calculateSellingPrice();
        $this->assertSame(300.0, $sellingPrice);

        //Due date will take place in more than 30 days
        $knownDate = Carbon::create(2020, 11, 15);
        Carbon::setTestNow($knownDate);
        $invoice = new Invoice();
        $invoice->setDueOn(new \DateTime('2020-12-31'));
        $invoice->setAmount(1000);
        $sellingPrice = $invoice->calculateSellingPrice();
        $this->assertSame(500.0, $sellingPrice );
    }

}