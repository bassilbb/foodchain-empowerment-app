<?php

namespace App\Services\Marketplace\PriceImport;

interface ProductPriceParser
{
    /**
     * @param  array<string, mixed>  $mapping  canonical field => original column/element name
     *
     * @throws \InvalidArgumentException when the file cannot be parsed safely
     */
    public function parse(string $absolutePath, array $mapping = []): ParsedPriceFile;
}
