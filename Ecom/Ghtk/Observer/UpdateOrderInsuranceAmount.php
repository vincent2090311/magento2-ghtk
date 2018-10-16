<?php
namespace Ecom\Ghtk\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class UpdateOrderInsuranceAmount implements ObserverInterface
{
    /**
     * Set payment insurance to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();
        $insuranceAmount = $quote->getInsurance();
        $baseInsuranceAmount = $quote->getBaseInsurance();
        if ($order->getShippingMethod() == 'ghtk_ghtk' && $insuranceAmount != 0 && $baseInsuranceAmount != 0) {
            $order->setData(\Ecom\Ghtk\Model\Carrier\Ghtk::INSURANCE_CODE, $insuranceAmount);
            $order->setData(\Ecom\Ghtk\Model\Carrier\Ghtk::BASE_INSURANCE_CODE, $baseInsuranceAmount);
        }
        return $this;
    }
}
