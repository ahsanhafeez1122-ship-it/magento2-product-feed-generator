<?php

namespace Ahsan\ProductFeed\Model\Writer;

use Magento\Framework\File\Csv;

class CsvWriter implements WriterInterface
{
    private Csv $csvProcessor;

    public function __construct(Csv $csvProcessor)
    {
        $this->csvProcessor = $csvProcessor;
    }

    public function write(array $rows, array $attributeCodes, string $filePath): void
    {
        $data = [$attributeCodes];

        foreach ($rows as $row) {
            $line = [];
            foreach ($attributeCodes as $code) {
                $line[] = $row[$code] ?? '';
            }
            $data[] = $line;
        }

        $this->csvProcessor->saveData($filePath, $data);
    }
}
