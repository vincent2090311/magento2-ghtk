<?php

namespace Ecom\Ghtk\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class Insurance extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $creditmemo->setInsurance(0);
        $creditmemo->setBaseInsurance(0);

        $amount = $creditmemo->getOrder()->getInsurance();
        $creditmemo->setInsurance($amount);

        $amount = $creditmemo->getOrder()->getBaseInsurance();
        $creditmemo->setBaseInsurance($amount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getInsurance());
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getBaseInsurance());

        return $this;
    }
}
