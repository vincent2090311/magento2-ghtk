<?php

namespace Ecom\Ghtk\Block\Sales\Totals;

class Insurance extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ecom\Ghtk\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ecom\Ghtk\Helper\Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * Initialize order totals array
     *
     * @return $this
     */
    public function initTotals()
    {
        /** @var $parent \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals */
        $parent = $this->getParentBlock();
        $source = $parent->getSource();
        if($source->getInsurance() > 0) {
            $insurance = new \Magento\Framework\DataObject(
                [
                    'code' => \Ecom\Ghtk\Model\Carrier\Ghtk::INSURANCE_CODE,
                    'field' => \Ecom\Ghtk\Model\Carrier\Ghtk::INSURANCE_CODE,
                    'value' => $source->getInsurance(),
                    'label' => $this->helperData->getTotalLabel()
                ]
            );
            $parent->addTotal($insurance, 'shipping');
        }
    }
}
