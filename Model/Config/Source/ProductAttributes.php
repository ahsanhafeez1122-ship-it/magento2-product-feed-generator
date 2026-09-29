<?php

namespace Ahsan\ProductFeed\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Lists actual product EAV attributes from the catalog rather than a
 * hard-coded list, so the admin dropdown always reflects attributes
 * that really exist on the store (including custom ones).
 */
class ProductAttributes implements OptionSourceInterface
{
    private AttributeCollectionFactory $attributeCollectionFactory;

    public function __construct(AttributeCollectionFactory $attributeCollectionFactory)
    {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    public function toOptionArray()
    {
        $options = [];
        $collection = $this->attributeCollectionFactory->create();

        foreach ($collection as $attribute) {
            $options[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getFrontendLabel() ?: $attribute->getAttributeCode(),
            ];
        }

        return $options;
    }
}
