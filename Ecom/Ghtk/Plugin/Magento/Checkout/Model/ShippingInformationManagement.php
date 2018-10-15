<?php

namespace Ecom\Ghtk\Plugin\Magento\Checkout\Model;

class ShippingInformationManagement
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Ecom\Ghtk\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Ecom\Ghtk\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Ecom\Ghtk\Helper\Data $helperData
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->helperData = $helperData;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setInsurance(0);
        $quote->setBaseInsurance(0);

        $shippingAddress = $addressInformation->getShippingAddress();
        if ($shippingAddress) {
            $insurance = $shippingAddress->getExtensionAttributes()->getInsurance();
            if ($insurance) {
                $insurance_amount = $this->helperData->getConfigValue('insurance_amount');
                $quote->setInsurance($insurance_amount);
                $quote->setBaseInsurance($insurance_amount);
            }
        }
    }
}

