<?php

namespace Ahsan\ProductFeed\Model\Writer;

class XmlWriter implements WriterInterface
{
    public function write(array $rows, array $attributeCodes, string $filePath): void
    {
        $xml = new \SimpleXMLElement('<products/>');

        foreach ($rows as $row) {
            $productNode = $xml->addChild('product');

            foreach ($attributeCodes as $code) {
                $value = (string) ($row[$code] ?? '');
                $productNode->addChild($code, htmlspecialchars($value, ENT_XML1));
            }
        }

        $xml->asXML($filePath);
    }
}
