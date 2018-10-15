<?php

namespace Ecom\Ghtk\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;

class GhtkConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Ecom\Ghtk\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Ecom\Ghtk\Helper\Data $helperData
     */
    public function __construct(
        \Ecom\Ghtk\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        if ($this->helperData->getConfigValue('active') && $this->helperData->getConfigValue('allow_use_insurance')) {
            $config['ghtk'] = [
                'insurance_message' => $this->helperData->getConfigValue('insurance_message'),
                'insurance_amount' => $this->helperData->getConfigValue('insurance_amount')
            ];
        }
        return $config;
    }
}
