<?php

namespace Ahsan\ProductFeed\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Categories implements OptionSourceInterface
{
    private CategoryCollectionFactory $categoryCollectionFactory;

    public function __construct(CategoryCollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function toOptionArray()
    {
        $options = [];
        $collection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('level', ['gt' => 1]);

        foreach ($collection as $category) {
            $options[] = [
                'value' => $category->getId(),
                'label' => $category->getName(),
            ];
        }

        return $options;
    }
}
