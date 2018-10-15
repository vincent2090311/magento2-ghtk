<?php

namespace Ecom\Ghtk\Model\Carrier;

use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Ghtk extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    const LBS_G = 453.59237;
    const KGS_G = 1000;
    const LBS_KG = 0.453592;
    const INSURANCE_CODE = 'insurance';
    const BASE_INSURANCE_CODE = 'base_insurance';

    /**
     * @var string
     */
    protected $_code = 'ghtk';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Model\Information
     */
    protected $_storeInformation;

    /**
     * @var \Ecom\Ghtk\Api\Adapter
     */
    protected $_apiAdapter;

    /**
     * @var string
     */
    protected $weightUnit;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Model\Information $storeInformation
     * @param \Ecom\Ghtk\Api\Adapter $apiAdapter
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\Information $storeInformation,
        \Ecom\Ghtk\Api\Adapter $apiAdapter,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_storeManager = $storeManager;
        $this->_storeInformation = $storeInformation;
        $this->_apiAdapter = $apiAdapter;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->weightUnit = $scopeConfig->getValue(\Magento\Directory\Helper\Data::XML_PATH_WEIGHT_UNIT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param RateRequest $request
     * @return Result|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $shippingPrice = $this->getShippingPrice($request);

        /** @var Result $result */
        $result = $this->_rateResultFactory->create();

        if ($shippingPrice !== false) {
            $method = $this->createResultMethod($shippingPrice);
            $result->append($method);
        }

        return $result;
    }

    /**
     * @param RateRequest $request
     * @param int $freeBoxes
     * @return bool|float
     */
    private function getShippingPrice(RateRequest $request)
    {
        $shippingPrice = false;

        $rate = $this->weightUnit == 'kgs' ? self::KGS_G : self::LBS_G;

        $storeInfo = $this->_storeInformation->getStoreInformationObject($this->_storeManager->getStore());

        $payload = [
            "pick_province" => $storeInfo->getData('region_id'),
            "pick_district" => $storeInfo->getData('city'),
            "pick_address" => $storeInfo->getData('street_line1'),
            "province" => $request->getDestRegionCode(),
            "district" => $request->getDestCity(),
            "address" => $request->getDestStreet(),
            "weight" => $request->getPackageWeight() * $rate,
            "value" => $request->getPackageValue()
        ];
        $esimate = $this->_apiAdapter->estimateShipping($payload);

        if ($esimate['success'] == true) {
            $shippingPrice = $esimate['fee']['fee'];
        }
        
        return $shippingPrice;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['ghtk' => $this->getConfigData('name')];
    }

    /**
     * @param int|float $shippingPrice
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function createResultMethod($shippingPrice)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);
        return $method;
    }
}
