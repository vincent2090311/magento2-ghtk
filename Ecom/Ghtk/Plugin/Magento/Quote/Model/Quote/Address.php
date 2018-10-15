<?php

namespace Ecom\Ghtk\Plugin\Magento\Quote\Model\Quote;

class Address
{
    /**
     * @param \Magento\Quote\Model\Quote\Address $subject
     */
    public function afterGetRegionCode(
        \Magento\Quote\Model\Quote\Address $subject,
        $result
    ) {
        if (!$result) {
            $region = $subject->getData('region');
            if (is_array($region)) {
                $result = $region['region_code'];
            }
        }
        return $result;
    }
}

