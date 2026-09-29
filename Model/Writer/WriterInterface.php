<?php

namespace Ahsan\ProductFeed\Model\Writer;

interface WriterInterface
{
    /**
     * @param array $rows Each row is an associative array of attribute_code => value
     * @param string[] $attributeCodes Column order
     * @param string $filePath Absolute path to write the feed file to
     */
    public function write(array $rows, array $attributeCodes, string $filePath): void;
}
