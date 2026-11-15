<?php

namespace App\Services\Marketplace\PriceImport;

class CsvProductPriceParser implements ProductPriceParser
{
    public const MAX_ROWS = 20000;

    public function parse(string $absolutePath, array $mapping = []): ParsedPriceFile
    {
        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            throw new \InvalidArgumentException('The uploaded CSV file could not be read.');
        }

        $sample = (string) file_get_contents($absolutePath, false, null, 0, 8192);
        if (str_contains($sample, "\0")) {
            throw new \InvalidArgumentException('The file contains binary data and is not a valid CSV price list.');
        }

        $handle = fopen($absolutePath, 'rb');
        if ($handle === false) {
            throw new \InvalidArgumentException('The uploaded CSV file could not be opened.');
        }

        try {
            $firstLine = (string) fgets($handle);
            rewind($handle);

            $delimiter = $this->detectDelimiter($firstLine);

            $header = fgetcsv($handle, 0, $delimiter);
            if ($header === false || $header === [null] || $header === []) {
                throw new \InvalidArgumentException('The CSV file is empty or has no header row.');
            }

            $header = array_map(fn ($value) => trim((string) $value), $header);
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]) ?? (string) $header[0];

            if (count(array_filter($header, fn ($value) => $value !== '')) === 0) {
                throw new \InvalidArgumentException('The CSV header row is empty.');
            }

            $suggested = FieldMapping::suggest($header);
            $mapping = FieldMapping::merge($suggested, $mapping, $header);

            $missing = FieldMapping::missingRequired($mapping);
            if ($missing !== []) {
                $labels = array_map(fn ($field) => FieldMapping::FIELDS[$field], $missing);

                throw new \InvalidArgumentException(
                    'Could not detect the following required columns: '.implode(', ', $labels).
                    '. Please map the fields manually.'
                );
            }

            $rows = [];
            $line = 1;

            while (($record = fgetcsv($handle, 0, $delimiter)) !== false) {
                $line++;

                if ($record === [null] || $record === []) {
                    continue;
                }

                $values = array_map(fn ($value) => trim((string) $value), $record);

                if (count(array_filter($values, fn ($value) => $value !== '')) === 0) {
                    continue;
                }

                $raw = [];
                foreach ($header as $index => $column) {
                    if ($column === '') {
                        continue;
                    }
                    $raw[$column] = $values[$index] ?? null;
                }

                $rows[] = [
                    'row_number' => $line,
                    'raw' => $raw,
                    'fields' => FieldMapping::apply($raw, $mapping),
                ];

                if (count($rows) > self::MAX_ROWS) {
                    throw new \InvalidArgumentException(
                        'The CSV contains more than '.number_format(self::MAX_ROWS).' records. Please split the file.'
                    );
                }
            }

            if ($rows === []) {
                throw new \InvalidArgumentException('The CSV file contains a header but no product records.');
            }

            return new ParsedPriceFile($rows, $header, $mapping, 'csv');
        } finally {
            fclose($handle);
        }
    }

    private function detectDelimiter(string $line): string
    {
        $candidates = [',' => 0, ';' => 0, "\t" => 0, '|' => 0];

        foreach ($candidates as $delimiter => $_) {
            $candidates[$delimiter] = substr_count($line, $delimiter);
        }

        arsort($candidates);
        $best = array_key_first($candidates);

        return ($candidates[$best] ?? 0) > 0 ? (string) $best : ',';
    }
}
