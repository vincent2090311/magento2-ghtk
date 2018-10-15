<?php

namespace Ecom\Ghtk\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Store\Model\ScopeInterface;
use Ecom\Ghtk\Model\Carrier\Ghtk;

/**
 * Sync Order
 */
class SyncOrder implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\Information
     */
    protected $storeInformation;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Ecom\Ghtk\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Ecom\Ghtk\Api\Adapter
     */
    protected $apiAdapter;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\Information $storeInformation
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Ecom\Ghtk\Helper\Data $helperData
     * @param \Ecom\Ghtk\Api\Adapter $apiAdapter
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\Information $storeInformation,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Ecom\Ghtk\Helper\Data $helperData,
        \Ecom\Ghtk\Api\Adapter $apiAdapter
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeInformation = $storeInformation;
        $this->orderRepository = $orderRepository;
        $this->helperData = $helperData;
        $this->apiAdapter = $apiAdapter;
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getShippingMethod() == 'ghtk_ghtk') {
            $storeInfo = $this->storeInformation->getStoreInformationObject($order->getStore());
            $weightUnit = $this->scopeConfig->getValue(\Magento\Directory\Helper\Data::XML_PATH_WEIGHT_UNIT, ScopeInterface::SCOPE_STORE);
            $rate = $weightUnit == 'kgs' ? 1 : Ghtk::LBS_KG;

            $address = $order->getShippingAddress();
            if (!$address) {
                $address = $order->getBillingAddress();
            }

            $products = [];
            $orderItems = $order->getAllVisibleItems();
            foreach ($orderItems as $item) {
                $products[] = [
                    "name" => $item->getName(),
                    "weight" => $item->getWeight() * $item->getQtyOrdered() * $rate,
                    "quantity" => (int) $item->getQtyOrdered()
                ];
            }

            $payload = [
                "order" => [
                    "id" => $order->getIncrementId(),
                    "pick_name" => $storeInfo->getData('name'),
                    "pick_address" => $storeInfo->getData('street_line1'),
                    "pick_province" => $storeInfo->getData('region_id'),
                    "pick_district" => $storeInfo->getData('city'),
                    "pick_tel" => $storeInfo->getData('phone'),

                    "name" => $address->getName(),
                    "address" => $address->getStreet()[0],
                    "province" => $address->getRegion(),
                    "district" => $address->getCity(),
                    "tel" => $address->getTelephone(),

                    "is_freeship" => 0,
                    "pick_money" => $this->helperData->getConfigValue('allow_pick_money') ? ($order->getGrandTotal() - $order->getShippingAmount()) : 0,
                    "value" => (int) $order->getInsurance()
                ],
                "products" => $products
            ];

            $response = $this->apiAdapter->syncOrder($payload);

            if ($response['success'] == true) {
                $order->setData('tracking_label', $response['order']['label']);
                $this->orderRepository->save($order);
            }
        }

        return $this;
    }
}