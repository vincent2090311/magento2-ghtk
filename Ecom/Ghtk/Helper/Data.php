<?php

namespace Ecom\Ghtk\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const CONFIG_PATH = 'carriers/ghtk/';

    /**
     * @return mixed
     */
    public function getConfigValue($path)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $value = $this->scopeConfig->getValue(self::CONFIG_PATH . $path, $storeScope);
        return $value;
    }

    /**
     * @return string
     */
    public function getTotalLabel()
    {
        return __('Insurance');
    }
}
