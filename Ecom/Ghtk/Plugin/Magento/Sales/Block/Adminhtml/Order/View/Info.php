<?php

namespace Ecom\Ghtk\Plugin\Magento\Sales\Block\Adminhtml\Order\View;

class Info
{
    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Info $subject
     * @param string $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(
        \Magento\Sales\Block\Adminhtml\Order\View\Info $subject,
        $result
    ) {
        $order = $subject->getOrder();
        $trackingBlock = $subject->getLayout()->getBlock('ghtk.tracking.label');
        if ($trackingBlock !== false && $subject->getNameInLayout() == 'order_info' && $order->getShippingMethod() == 'ghtk_ghtk') {
            $trackingBlock->setOrder($order);
            $result = $result . $trackingBlock->toHtml();
        }
        return $result;
    }
}