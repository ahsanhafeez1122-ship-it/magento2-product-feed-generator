<?php

namespace Ahsan\ProductFeed\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class FeedFormat implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'csv', 'label' => __('CSV')],
            ['value' => 'xml', 'label' => __('XML')],
        ];
    }
}
