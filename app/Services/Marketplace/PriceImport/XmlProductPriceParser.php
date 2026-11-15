<?php

namespace App\Services\Marketplace\PriceImport;

use DOMDocument;
use DOMElement;
use DOMXPath;

class XmlProductPriceParser implements ProductPriceParser
{
    public const MAX_ROWS = 20000;

    private const RECORD_TAGS = ['product', 'item', 'row', 'record', 'entry'];

    public function parse(string $absolutePath, array $mapping = []): ParsedPriceFile
    {
        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            throw new \InvalidArgumentException('The uploaded XML file could not be read.');
        }

        $contents = (string) file_get_contents($absolutePath);

        if (str_contains($contents, "\0")) {
            throw new \InvalidArgumentException('The file contains binary data and is not a valid XML price list.');
        }

        // XXE protection: refuse documents that declare entities or a DTD.
        if (preg_match('/<!DOCTYPE|<!ENTITY/i', $contents) === 1) {
            throw new \InvalidArgumentException('The XML file contains a DTD or entity declaration, which is not allowed.');
        }

        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            $document = new DOMDocument();
            $loaded = $document->loadXML($contents, LIBXML_NONET | LIBXML_NOBLANKS | LIBXML_COMPACT);
            $errors = libxml_get_errors();
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        if (! $loaded || $document->documentElement === null) {
            $message = $errors[0]->message ?? 'Malformed XML document.';

            throw new \InvalidArgumentException('Invalid XML: '.trim($message));
        }

        $records = $this->findRecords($document);

        if ($records === []) {
            throw new \InvalidArgumentException('The XML file does not contain any product records.');
        }

        if (count($records) > self::MAX_ROWS) {
            throw new \InvalidArgumentException(
                'The XML contains more than '.number_format(self::MAX_ROWS).' records. Please split the file.'
            );
        }

        $rawRows = [];
        $headers = [];

        foreach ($records as $index => $record) {
            $raw = $this->extractRow($record);
            if ($raw === []) {
                continue;
            }

            $rawRows[] = ['row_number' => $index + 1, 'raw' => $raw];

            foreach (array_keys($raw) as $key) {
                if (! in_array($key, $headers, true)) {
                    $headers[] = $key;
                }
            }
        }

        if ($rawRows === []) {
            throw new \InvalidArgumentException('The XML file does not contain any readable product records.');
        }

        $suggested = FieldMapping::suggest($headers);
        $mapping = FieldMapping::merge($suggested, $mapping, $headers);

        $missing = FieldMapping::missingRequired($mapping);
        if ($missing !== []) {
            $labels = array_map(fn ($field) => FieldMapping::FIELDS[$field], $missing);

            throw new \InvalidArgumentException(
                'Could not detect the following required elements: '.implode(', ', $labels).
                '. Please map the fields manually.'
            );
        }

        $rows = [];
        foreach ($rawRows as $row) {
            $rows[] = [
                'row_number' => $row['row_number'],
                'raw' => $row['raw'],
                'fields' => FieldMapping::apply($row['raw'], $mapping),
            ];
        }

        return new ParsedPriceFile($rows, $headers, $mapping, 'xml');
    }

    /**
     * @return array<int, DOMElement>
     */
    private function findRecords(DOMDocument $document): array
    {
        $xpath = new DOMXPath($document);

        foreach (self::RECORD_TAGS as $tag) {
            $nodes = $xpath->query('//*[local-name()="'.$tag.'"]');
            if ($nodes !== false && $nodes->length > 0) {
                $records = [];
                foreach ($nodes as $node) {
                    if ($node instanceof DOMElement) {
                        $records[] = $node;
                    }
                }

                return $records;
            }
        }

        $root = $document->documentElement;
        if ($root === null) {
            return [];
        }

        $records = [];
        foreach ($root->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $records[] = $child;
            }
        }

        // A single-record document where the root itself holds the fields.
        if ($records === []) {
            $records[] = $root;
        }

        return $records;
    }

    /**
     * @return array<string, string>
     */
    private function extractRow(DOMElement $record): array
    {
        $raw = [];

        foreach ($record->childNodes as $child) {
            if (! $child instanceof DOMElement) {
                continue;
            }

            $key = $child->localName ?: $child->nodeName;
            if ($key === '' || array_key_exists($key, $raw)) {
                continue;
            }

            $raw[$key] = trim($child->textContent);
        }

        if ($record->hasAttributes()) {
            foreach ($record->attributes as $attribute) {
                $key = $attribute->nodeName;
                if ($key !== '' && ! array_key_exists($key, $raw)) {
                    $raw[$key] = trim($attribute->nodeValue);
                }
            }
        }

        return $raw;
    }
}
