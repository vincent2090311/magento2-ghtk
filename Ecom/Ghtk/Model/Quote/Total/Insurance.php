<?php

namespace Ecom\Ghtk\Model\Quote\Total;

use Magento\Store\Model\ScopeInterface;

class Insurance extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Ecom\Ghtk\Helper\Data
     */
    protected $helperData;

    /**
     * Collect grand total address amount
     *
     * @param \Ecom\Ghtk\Helper\Data $helperData
     */
    public function __construct(
        \Ecom\Ghtk\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        if (!count($shippingAssignment->getItems())) {
            return $this;
        }

        if ($this->helperData->getConfigValue('active') && 
            $this->helperData->getConfigValue('allow_use_insurance')
        ) {
            $insuranceAmount = $quote->getInsurance();

            $total->setTotalAmount(\Ecom\Ghtk\Model\Carrier\Ghtk::INSURANCE_CODE, $insuranceAmount);
            $total->setBaseTotalAmount(\Ecom\Ghtk\Model\Carrier\Ghtk::BASE_INSURANCE_CODE, $insuranceAmount);

            $total->setInsurance($insuranceAmount);
            $total->setBaseInsurance($insuranceAmount);

            $quote->setInsurance($insuranceAmount);
            $quote->setBaseInsurance($insuranceAmount);

            // $total->setGrandTotal($total->getGrandTotal());
            // $total->setBaseGrandTotal($total->getBaseGrandTotal());
        }
        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $insuranceAmount = $quote->getInsurance();
        $result = [];
        if ($this->helperData->getConfigValue('active') && 
            $this->helperData->getConfigValue('allow_use_insurance') && 
            $insuranceAmount > 0
        ) {
            $result = [
                'code' => \Ecom\Ghtk\Model\Carrier\Ghtk::INSURANCE_CODE,
                'title' => $this->getLabel(),
                'value' => $insuranceAmount
            ];
        }
        return $result;
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return $this->helperData->getTotalLabel();
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     */
    protected function clearValues(\Magento\Quote\Model\Quote\Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }
}
