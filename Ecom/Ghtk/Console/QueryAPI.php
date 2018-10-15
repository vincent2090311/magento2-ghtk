<?php

namespace Ecom\Ghtk\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueryAPI extends Command
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    private $objectManagerFactory;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\App\ObjectManagerFactory $objectManagerFactory
     */
    public function __construct(
        \Magento\Framework\App\ObjectManagerFactory $objectManagerFactory
    ) {
        $this->objectManagerFactory = $objectManagerFactory;
        parent::__construct();
    }

    protected function getObjectManager()
    {
        if (null == $this->objectManager) {
            $area = \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;
            $this->objectManager = $this->objectManagerFactory->create($_SERVER);
            /** @var \Magento\Framework\App\State $appState */
            $appState = $this->objectManager->get('Magento\Framework\App\State');
            $appState->setAreaCode($area);
            $configLoader = $this->objectManager->get('Magento\Framework\ObjectManager\ConfigLoaderInterface');
            $this->objectManager->configure($configLoader->load($area));
        }
        return $this->objectManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("ghtk:query");
        $this->setDescription("Query to test Ghtk API");
        // $this->setDefinition([
        //     new InputArgument('order', InputArgument::REQUIRED, "OrderId")
        // ]);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $objectManager = $this->getObjectManager();
        $apiAdapter = $objectManager->create('Ecom\Ghtk\Api\Adapter');

        // $data = array(
        //     "pick_province" => "Hà Nội",
        //     "pick_district" => "Quận Hai Bà Trưng",
        //     "province" => "Hà nội",
        //     "district" => "Quận Cầu Giấy",
        //     "address" => "P.503 tòa nhà Âu Việt, số 1 Lê Đức Thọ",
        //     "weight" => 1000,
        //     "value" => 3000000,
        // );
        // print_r($apiAdapter->estimateShipping($data));

        // $data = [
        //     "products": [
        //         {
        //             "name": "bút",
        //             "weight": 0.1,
        //             "quantity": 1
        //         }, 
        //         {
        //             "name": "tẩy",
        //             "weight": 0.2,
        //             "quantity": 1
        //         }
        //     ],
        //     "order": {
        //         "id": "a4",
        //         "pick_name": "HCM-nội thành",
        //         "pick_address": "590 CMT8 P.11",
        //         "pick_province": "TP. Hồ Chí Minh",
        //         "pick_district": "Quận 3",
        //         "pick_tel": "0911222333",
        //         "tel": "0911222333",
        //         "name": "Ghtk - HCM - Noi Thanh",
        //         "address": "123 nguyễn chí thanh",
        //         "province": "TP. Hồ Chí Minh",
        //         "district": "Quận 1",
        //         "is_freeship": "1",
        //         "pick_date": "2016-09-30",
        //         "pick_money": 47000,
        //         "note": "Khối lượng tính cước tối đa: 1.00 kg",
        //         "value": 3000000
        //     }
        // ];

        $orderModel = $objectManager->create('Magento\Sales\Model\Order');
        $order = $orderModel->loadByIncrementId('000000050');
        $storeInfo = $objectManager->create('Magento\Store\Model\Information')->getStoreInformationObject($order->getStore());

        $address = $order->getShippingAddress();
        if (!$address) {
            $address = $order->getBillingAddress();
        }

        $products = [];
        $orderItems = $order->getAllVisibleItems();
        foreach ($orderItems as $item) {
            $products[] = [
                "name" => $item->getName(),
                "weight" => $item->getWeight() * $item->getQtyOrdered(),
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

                "is_freeship" => "1",

                "pick_money" => 0,
                "value" => (int) $order->getInsurance()
            ],
            "products" => $products
        ];

        // $response = $apiAdapter->syncOrder($payload);
    }
}