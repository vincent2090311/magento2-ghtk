<?php

namespace Ecom\Ghtk\Api;

use Magento\Store\Model\ScopeInterface;

class Adapter
{
    const SYNC_ORDER = '/services/shipment/order';
    const ESTIMATE_SHIPPING = '/services/shipment/fee';
    const GET_ORDER_STATUS = '/services/shipment/v2';
    const CANCEL_ORDER = '/services/shipment/cancel';
    const PRINT_ORDER = '/services/label';

    const API_URL = 'carriers/ghtk/api_url';
    const API_KEY = 'carriers/ghtk/api_key';

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * constructor
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->curl = $curl;
        $this->serializer = $serializer;
        $this->apiUrl = $scopeConfig->getValue(static::API_URL, ScopeInterface::SCOPE_WEBSITES);
        $this->apiKey = $scopeConfig->getValue(static::API_KEY, ScopeInterface::SCOPE_WEBSITES);

        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Token", $this->apiKey);
        $this->curl->setOption(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    }

    /*
    {
        "products": [{
            "name": "bút",
            "weight": 0.1,
            "quantity": 1
        }, {
            "name": "tẩy",
            "weight": 0.2,
            "quantity": 1
        }],
        "order": {
            "id": "a4",
            "pick_name": "HCM-nội thành",
            "pick_address": "590 CMT8 P.11",
            "pick_province": "TP. Hồ Chí Minh",
            "pick_district": "Quận 3",
            "pick_tel": "0911222333",
            "tel": "0911222333",
            "name": "Ghtk - HCM - Noi Thanh",
            "address": "123 nguyễn chí thanh",
            "province": "TP. Hồ Chí Minh",
            "district": "Quận 1",
            "is_freeship": "1",
            "pick_date": "2016-09-30",
            "pick_money": 47000,
            "note": "Khối lượng tính cước tối đa: 1.00 kg",
            "value": 3000000
        }
    }
    */
    public function syncOrder(array $params): array
    {
        try {
            $url = $this->apiUrl . static::SYNC_ORDER;
            $this->curl->post($url, $this->serializer->serialize($params));
            $response = $this->curl->getBody();
            $result = $this->serializer->unserialize($response);
        } catch (\Exception $e) {
            $result = [];
        }

        return $result;
    }

    /*
    {
        "pick_province": "Hà Nội",
        "pick_district": "Quận Hai Bà Trưng",
        "province": "Hà nội",
        "district": "Quận Cầu Giấy",
        "address": "P.503 tòa nhà Âu Việt, số 1 Lê Đức Thọ",
        "weight": 1000,
        "value": 3000000
    }
    */
    public function estimateShipping(array $params): array
    {
        try {
            $url = $this->apiUrl . static::ESTIMATE_SHIPPING . '?' . http_build_query($params);
            $this->curl->get($url);
            $response = $this->curl->getBody();
            $result = $this->serializer->unserialize($response);
        } catch (\Exception $e) {
            $result = [];
        }

        return $result;
    }

    public function getOrderStatus(string $trackingLabel): array
    {
        try {
            $url = $this->apiUrl . static::GET_ORDER_STATUS . '/' . $trackingLabel;
            $this->curl->get($url);
            $response = $this->curl->getBody();
            $result = $this->serializer->unserialize($response);
        } catch (\Exception $e) {
            $result = [];
        }

        return $result;
    }

    public function cancelOrder(string $trackingLabel): array
    {
        try {
            $url = $this->apiUrl . static::CANCEL_ORDER . '/' . $trackingLabel;;
            $this->curl->post($url, []);
            $response = $this->curl->getBody();
            $result = $this->serializer->unserialize($response);
        } catch (\Exception $e) {
            $result = [];
        }

        return $result;
    }
}