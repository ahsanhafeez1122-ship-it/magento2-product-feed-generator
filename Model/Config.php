<?php

namespace Ahsan\ProductFeed\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Single source of truth for every admin-configurable value the feed
 * generator uses. Nothing about the feed (format, attributes, filename,
 * categories) is hard-coded in the generator itself — it all comes
 * from here, which reads Stores > Configuration > Product Feed Generator.
 */
class Config
{
    private const XML_PATH_ENABLED = 'productfeed/general/enabled';
    private const XML_PATH_FORMAT = 'productfeed/general/format';
    private const XML_PATH_FILENAME = 'productfeed/general/filename';
    private const XML_PATH_INCLUDED_ATTRIBUTES = 'productfeed/attributes/included_attributes';
    private const XML_PATH_CATEGORY_IDS = 'productfeed/attributes/category_ids';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getFormat(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_FORMAT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getFilename(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_FILENAME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string[]
     */
    public function getIncludedAttributes(?int $storeId = null): array
    {
        $value = (string) $this->scopeConfig->getValue(
            self::XML_PATH_INCLUDED_ATTRIBUTES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return array_filter(explode(',', $value));
    }

    /**
     * @return int[]
     */
    public function getCategoryIds(?int $storeId = null): array
    {
        $value = (string) $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_IDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return array_map('intval', array_filter(explode(',', $value)));
    }
}
