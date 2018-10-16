<?php
namespace Ecom\Ghtk\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CancelOrder implements ObserverInterface
{
    /**
     * @var \Ecom\Ghtk\Api\Adapter
     */
    protected $apiAdapter;

    /**
     * Constructor
     *
     * @param \Ecom\Ghtk\Api\Adapter $apiAdapter
     */
    public function __construct(
        \Ecom\Ghtk\Api\Adapter $apiAdapter
    ) {
        $this->apiAdapter = $apiAdapter;
    }

    /**
     * Cancel order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order->getShippingMethod() == 'ghtk_ghtk' && $order->getTrackingLabel()) {
            $this->apiAdapter->cancelOrder($order->getTrackingLabel());
        }
        return $this;
    }
}
