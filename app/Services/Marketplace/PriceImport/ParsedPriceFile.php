<?php

namespace App\Services\Marketplace\PriceImport;

class ParsedPriceFile
{
    /**
     * @param  array<int, array{row_number: int, raw: array<string, mixed>, fields: array<string, string|null>}>  $rows
     * @param  array<int, string>  $headers
     * @param  array<string, string>  $mapping
     */
    public function __construct(
        public readonly array $rows,
        public readonly array $headers,
        public readonly array $mapping,
        public readonly string $format,
    ) {
    }

    public function count(): int
    {
        return count($this->rows);
    }
}
