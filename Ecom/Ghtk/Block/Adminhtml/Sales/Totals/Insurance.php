<?php

namespace Ecom\Ghtk\Block\Adminhtml\Sales\Totals;

class Insurance extends \Magento\Sales\Block\Adminhtml\Order\Totals
{
    /**
     * @var \Ecom\Ghtk\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Ecom\Ghtk\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Ecom\Ghtk\Helper\Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->helperData = $helperData;
    }

    /**
     *
     *
     * @return $this
     */
    public function initTotals()
    {
        $source = $this->getSource();
        if(!$source->getInsurance()) {
            return $this;
        }
        $total = new \Magento\Framework\DataObject(
            [
                'code' => \Ecom\Ghtk\Model\Carrier\Ghtk::INSURANCE_CODE,
                'value' => $source->getInsurance(),
                'label' => $this->helperData->getTotalLabel()
            ]
        );
        $this->getParentBlock()->addTotal($total, 'shipping');

        return $this;
    }
}
