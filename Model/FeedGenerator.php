<?php

namespace Ahsan\ProductFeed\Model;

use Ahsan\ProductFeed\Model\Writer\WriterInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Psr\Log\LoggerInterface;

/**
 * Builds the product feed using only what the admin has configured:
 * which attributes to include, which categories to limit to, and which
 * format to write. Adding a new attribute or switching format is a
 * configuration change in the admin, never a code change here.
 */
class FeedGenerator
{
    private Config $config;
    private ProductCollectionFactory $productCollectionFactory;
    private Filesystem $filesystem;
    private LoggerInterface $logger;

    /**
     * @var WriterInterface[] Keyed by format (e.g. ['csv' => CsvWriter, 'xml' => XmlWriter]), wired in di.xml
     */
    private array $writers;

    public function __construct(
        Config $config,
        ProductCollectionFactory $productCollectionFactory,
        Filesystem $filesystem,
        LoggerInterface $logger,
        array $writers = []
    ) {
        $this->config = $config;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->writers = $writers;
    }

    /**
     * @return string Absolute path of the generated feed file
     * @throws LocalizedException
     */
    public function generate(?int $storeId = null): string
    {
        if (!$this->config->isEnabled($storeId)) {
            throw new LocalizedException(__('Product feed generation is disabled in configuration.'));
        }

        $format = $this->config->getFormat($storeId);

        if (!isset($this->writers[$format])) {
            throw new LocalizedException(__('No writer is registered for feed format "%1".', $format));
        }

        $attributeCodes = $this->config->getIncludedAttributes($storeId);

        if (empty($attributeCodes)) {
            throw new LocalizedException(__('No attributes are selected for the product feed.'));
        }

        $rows = $this->collectProductData($attributeCodes, $this->config->getCategoryIds($storeId));

        $filePath = $this->resolveFilePath($this->config->getFilename($storeId), $format);

        $this->writers[$format]->write($rows, $attributeCodes, $filePath);

        $this->logger->info(sprintf(
            'Ahsan_ProductFeed: generated %s with %d product(s) at %s',
            $format,
            count($rows),
            $filePath
        ));

        return $filePath;
    }

    private function collectProductData(array $attributeCodes, array $categoryIds): array
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect($attributeCodes);
        $collection->addAttributeToFilter('status', 1);

        if (!empty($categoryIds)) {
            $collection->addCategoriesFilter(['in' => $categoryIds]);
        }

        $rows = [];

        foreach ($collection as $product) {
            $row = [];
            foreach ($attributeCodes as $code) {
                $row[$code] = $product->getData($code);
            }
            $rows[] = $row;
        }

        return $rows;
    }

    private function resolveFilePath(string $filename, string $format): string
    {
        $exportDir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $relativePath = 'export/' . $filename . '.' . $format;

        return $exportDir->getAbsolutePath($relativePath);
    }
}
