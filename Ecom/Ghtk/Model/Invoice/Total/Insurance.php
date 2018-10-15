<?php

namespace Ecom\Ghtk\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Insurance extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoice->setInsurance(0);
        $invoice->setBaseInsurance(0);

        $amount = $invoice->getOrder()->getInsurance();
        $invoice->setInsurance($amount);
        $amount = $invoice->getOrder()->getBaseInsurance();
        $invoice->setBaseInsurance($amount);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getInsurance());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getInsurance());

        return $this;
    }
}
