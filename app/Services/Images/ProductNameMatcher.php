<?php

namespace App\Services\Images;

/**
 * Compares a marketplace/supplier product name with a catalogue product name.
 *
 * Supplier price lists use terse wholesale descriptions
 * ("CHCEKERS CUSTARD PAP REFILL 400G*8") while retail catalogues use
 * shopper-facing ones ("Checkers Custard Pap Refill 400g"). This matcher
 * normalises both, tolerates typos and pack-count noise, and refuses matches
 * whose pack sizes, product types, packaging forms or flavours disagree.
 *
 * It is deliberately conservative: callers should require a high score, a
 * matching brand token, a clear margin over the runner-up and no conflict
 * before treating a candidate as genuine.
 */
class ProductNameMatcher
{
    /** Tokens that carry no product identity. */
    private const NOISE_TOKENS = [
        'promo', 'promotion', 'pack', 'packs', 'pieces', 'piece', 'pcs', 'carton',
        'cartons', 'by', 'and', 'the', 'of', 'with', 'free', 'new', 'offer',
        'in', 'per', 'plus',
    ];

    /** Wholesale abbreviations mapped to the catalogue spelling. */
    private const CANONICAL_TOKENS = [
        'semo' => 'semolina',
        'semovita' => 'semovita',
        'refil' => 'refill',
        'refill' => 'refill',
        'vannina' => 'vanilla',
        'vanila' => 'vanilla',
        'noddles' => 'noodles',
        'noddle' => 'noodles',
        'veg' => 'vegetable',
        'vege' => 'vegetable',
        'spaghetini' => 'spaghettini',
        'spagetti' => 'spaghetti',
        'fc' => 'milk',
        'sb' => 'strawberry',
        'pet' => 'bottle',
    ];

    /**
     * Mutually exclusive flavour/variant words. If both names declare a
     * variant and they disagree, the candidate is a different product even
     * when the brand and pack size are identical.
     */
    private const VARIANT_TOKENS = [
        'chicken', 'onion', 'jollof', 'beef', 'shrimp', 'prawn', 'curry',
        'barbecue', 'bbq', 'suya', 'pepper', 'banana', 'vanilla', 'chocolate',
        'strawberry', 'orange', 'apple', 'pineapple', 'mango', 'coconut',
        'caramel', 'grape', 'lemon', 'lime', 'mint', 'coffee', 'cappuccino',
        'ginger', 'garlic', 'tomato', 'groundnut', 'peanut',
        'plain', 'vannina', 'goat', 'assorted', 'hot',
    ];

    /**
     * What each brand's "cooking oil" actually is. Source listings often say
     * only "cooking oil"; this lets us refuse a soya-oil product being given a
     * vegetable-oil pack shot (and vice versa) instead of guessing.
     *
     * Every entry below is confirmed by the manufacturer or a major retailer;
     * do not add a brand here on assumption alone, because a wrong entry either
     * accepts the wrong pack shot or refuses a correct one:
     *  - Golden Terra: goldenterra.com ("Golden Terra Soya Oil").
     *  - Grand: grandcereals.com ("Grand Pure Soya Oil").
     *  - Kings: devonkings.com.ng / Supermart / Jumia ("Kings Vegetable Oil").
     *  - Mamador: mamador.com.ng ("Mamador Pure Vegetable Oil").
     *  - Power: poweroil.com.ng ("Power Oil Veg", "vegetable cooking oil").
     *  - Emapoa: Emapoa Palm Oil (Konga; the supplier's own listing).
     *
     * @var array<string, string>
     */
    private const BRAND_OIL_SUBTYPES = [
        'goldenterra' => 'soya',
        'terra' => 'soya',
        'grand' => 'soya',
        'power' => 'vegetable',
        'mamador' => 'vegetable',
        'kings' => 'vegetable',
        'emapoa' => 'palm',
    ];

    /**
     * Product families. Two names that both name a family from this map but
     * share none are different products (wheat is not semolina, spaghetti is
     * not spaghettini, custard is not milk).
     */
    private const TYPE_TOKENS = [
        'semolina' => 'semolina',
        'semovita' => 'semovita',
        'wheat' => 'wheat',
        'garri' => 'garri',
        'gari' => 'garri',
        'rice' => 'rice',
        'beans' => 'beans',
        'bean' => 'beans',
        'maize' => 'maize',
        'corn' => 'maize',
        'noodles' => 'noodles',
        'spaghetti' => 'spaghetti',
        'spaghettini' => 'spaghettini',
        'spaghettini.' => 'spaghettini',
        'macaroni' => 'macaroni',
        'twist' => 'twist',
        'custard' => 'custard',
        'milk' => 'milk',
        'yoghurt' => 'yoghurt',
        'yogurt' => 'yoghurt',
        'oil' => 'oil',
        'sugar' => 'sugar',
        'tomato' => 'tomato',
        'tomatoes' => 'tomato',
        'mayonnaise' => 'mayonnaise',
        'mayo' => 'mayonnaise',
        'spread' => 'spread',
        'tea' => 'tea',
        'coffee' => 'coffee',
        'flour' => 'flour',
        'oats' => 'oats',
        'oat' => 'oats',
        'cereal' => 'cereal',
        'salt' => 'salt',
        'honey' => 'honey',
        'butter' => 'butter',
        'margarine' => 'butter',
        'cheese' => 'cheese',
        'water' => 'water',
        'juice' => 'juice',
    ];

    /**
     * Oil subtypes. A "cooking oil" listing is compatible with any of them, but
     * two different declared subtypes are not compatible with each other.
     *
     * @var array<string, string>
     */
    private const OIL_SUBTYPE_TOKENS = [
        'soya' => 'soya',
        'soyabean' => 'soya',
        'soy' => 'soya',
        'vegetable' => 'vegetable',
        'palm' => 'palm',
        'groundnut' => 'groundnut',
        'peanut' => 'groundnut',
    ];

    /**
     * Packaging forms. A jar photo is not a refill photo, so a disagreement
     * between two declared forms is fatal.
     */
    private const FORM_TOKENS = [
        'jar' => 'jar',
        'refill' => 'refill',
        'pillow' => 'pillow',
        'sachet' => 'sachet',
        'tin' => 'tin',
        'bottle' => 'bottle',
        'carton' => 'carton',
    ];

    /**
     * Features that change the product itself. Declared on one side only is
     * enough to refuse (a "3 in 1" sachet is not the plain pap refill).
     */
    private const EXCLUSIVE_FEATURES = ['3in1'];

    /**
     * Full comparison for one query/candidate pair.
     *
     * @return array{
     *     score: float,
     *     brand_match: bool,
     *     size_conflict: bool,
     *     variant_conflict: bool,
     *     type_conflict: bool,
     *     form_conflict: bool,
     *     oil_conflict: bool,
     *     feature_conflict: bool,
     *     conflict: bool,
     *     query_tokens: array<int, string>,
     *     candidate_tokens: array<int, string>,
     *     query_size: ?string,
     *     candidate_size: ?string
     * }
     */
    public function compare(string $query, string $candidate): array
    {
        $queryTokens = $this->tokens($query);
        $candidateTokens = $this->tokens($candidate);

        // "COOLCOW" vs "cool cow": join adjacent tokens that one side writes
        // as a single compound word.
        $candidateTokens = $this->alignCompounds($queryTokens, $candidateTokens);
        $queryTokens = $this->alignCompounds($candidateTokens, $queryTokens);

        // Retail names are more descriptive than wholesale ones ("Dano full
        // cream milk powder 2kg" vs "DANO FULLCREAM 2KG"). When every token of
        // the supplier name is present in the candidate, the extra words are
        // description, not a different product.
        $containment = $this->containsAll($queryTokens, $candidateTokens);

        $score = $containment ? 1.0 : $this->dice($queryTokens, $candidateTokens);
        $brandMatch = $this->brandMatches($queryTokens, $candidateTokens);

        $querySize = $this->size($query);
        $candidateSize = $this->size($candidate);

        $sizeConflict = $this->sizeConflicts($querySize, $candidateSize);
        $variantConflict = $this->variantConflicts($queryTokens, $candidateTokens);
        $typeConflict = $this->disjoint($queryTokens, $candidateTokens, self::TYPE_TOKENS);
        $formConflict = $this->disjoint($queryTokens, $candidateTokens, self::FORM_TOKENS);
        $oilConflict = $this->oilSubtypeConflicts($queryTokens, $candidateTokens);
        $featureConflict = $this->featureConflicts($queryTokens, $candidateTokens);

        return [
            'score' => round($score, 4),
            'containment' => $containment,
            'brand_match' => $brandMatch,
            'size_conflict' => $sizeConflict,
            'variant_conflict' => $variantConflict,
            'type_conflict' => $typeConflict,
            'form_conflict' => $formConflict,
            'oil_conflict' => $oilConflict,
            'feature_conflict' => $featureConflict,
            'conflict' => $sizeConflict || $variantConflict || $typeConflict || $formConflict || $oilConflict || $featureConflict,
            'query_tokens' => $queryTokens,
            'candidate_tokens' => $candidateTokens,
            'query_size' => $querySize,
            'candidate_size' => $candidateSize,
        ];
    }

    /**
     * Merge adjacent tokens in $tokens whose concatenation appears in $reference.
     *
     * @param  array<int, string>  $reference
     * @param  array<int, string>  $tokens
     * @return array<int, string>
     */
    private function alignCompounds(array $reference, array $tokens): array
    {
        $aligned = [];
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            if ($i + 1 < $count && in_array($tokens[$i].$tokens[$i + 1], $reference, true)) {
                $aligned[] = $tokens[$i].$tokens[$i + 1];
                $i++;
                continue;
            }

            $aligned[] = $tokens[$i];
        }

        return $aligned;
    }

    /**
     * True when every identity token of the supplier name appears in the
     * catalogue name.
     *
     * Packaging-form words are ignored on the supplier side: the candidate
     * simply not saying "bottle" is already allowed by the conflict rules, so
     * requiring it here would reject valid matches. A generic "cooking oil"
     * candidate is also credited with the subtype its brand actually bottles.
     *
     * @param  array<int, string>  $query
     * @param  array<int, string>  $candidate
     */
    private function containsAll(array $query, array $candidate): bool
    {
        $identity = array_values(array_filter(
            $query,
            fn (string $token): bool => ! isset(self::FORM_TOKENS[$token])
        ));

        if (count($identity) < 2) {
            return false;
        }

        $reference = $candidate;

        if (in_array('oil', $reference, true)) {
            $brand = $this->brandKey($reference);
            $inferred = $brand !== null ? (self::BRAND_OIL_SUBTYPES[$brand] ?? null) : null;

            if ($inferred !== null && ! in_array($inferred, $reference, true)) {
                $reference[] = $inferred;
            }
        }

        foreach ($identity as $token) {
            $found = false;

            foreach ($reference as $candidateToken) {
                if ($this->tokenSimilarity($token, $candidateToken) >= 0.85) {
                    $found = true;
                    break;
                }
            }

            if (! $found) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, string>
     */
    public function tokens(string $name): array
    {
        $normalized = $this->normalize($name);

        // Drop pack-count noise: "45g*100 by 11 promo" -> "45g".
        $normalized = preg_replace('/\bby\s+\d+\b/', ' ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\bx\s*\d+\b/', ' ', $normalized) ?? $normalized;

        $tokens = preg_split('/\s+/', trim($normalized)) ?: [];

        $tokens = array_values(array_filter($tokens, function (string $token): bool {
            if ($token === '' || in_array($token, self::NOISE_TOKENS, true)) {
                return false;
            }

            // Bare integers are pack counts (12, 24, 100) except product codes
            // such as Peak "123" / "456", which identify a different variant.
            if (preg_match('/^\d+$/', $token)) {
                return strlen($token) >= 3 && (int) $token % 10 !== 0;
            }

            return true;
        }));

        $tokens = array_map(
            fn (string $token): string => self::CANONICAL_TOKENS[$token] ?? $token,
            $tokens
        );

        // "G.P" arrives as one token because decimals keep their dot.
        $tokens = array_map(
            fn (string $token): string => preg_match('/^[a-z.]+$/', $token)
                ? str_replace('.', '', $token)
                : $token,
            $tokens
        );

        // Merge a bare unit with the preceding number: "5 lt" -> "5l".
        $units = [
            'kg' => 'kg', 'g' => 'g', 'mg' => 'mg',
            'l' => 'l', 'lt' => 'l', 'ltr' => 'l', 'lts' => 'l',
            'litre' => 'l', 'litres' => 'l', 'liter' => 'l', 'liters' => 'l',
            'ml' => 'ml', 'cl' => 'cl',
        ];

        $merged = [];
        foreach ($tokens as $token) {
            $previous = end($merged);

            if ($previous !== false && preg_match('/^\d+(?:\.\d+)?$/', $previous)
                && isset($units[$token])) {
                array_pop($merged);
                $merged[] = $previous.$units[$token];
                continue;
            }

            $merged[] = $token;
        }

        // Already-joined sizes: "25lt" -> "25l", "1.5ltr" -> "1.5l".
        $merged = array_map(function (string $token) use ($units): string {
            if (preg_match('/^(\d+(?:\.\d+)?)([a-z]+)$/', $token, $match) && isset($units[$match[2]])) {
                return $match[1].$units[$match[2]];
            }

            return $token;
        }, $merged);

        // Collapse dotted initials: "G.P Semovita" -> "gp semovita".
        $collapsed = [];
        $run = [];

        foreach ($merged as $token) {
            if (strlen($token) === 1) {
                $run[] = $token;
                continue;
            }

            if (count($run) >= 2) {
                $collapsed[] = implode('', $run);
            } elseif (count($run) === 1) {
                $collapsed[] = $run[0];
            }

            $run = [];
            $collapsed[] = $token;
        }

        if (count($run) >= 2) {
            $collapsed[] = implode('', $run);
        } elseif (count($run) === 1) {
            $collapsed[] = $run[0];
        }

        return array_values(array_unique($collapsed));
    }

    public function normalize(string $name): string
    {
        $name = strtolower($name);
        $name = str_replace(['&', '*', '×'], [' and ', ' ', ' '], $name);
        $name = str_replace(["'", '’'], '', $name);

        // "3 in 1" / "3-in-1" / "3in1" all describe the same formulation.
        $name = preg_replace('/\b3\s*[-\s]\s*in\s*[-\s]\s*1\b/', '3in1', $name) ?? $name;

        $name = preg_replace('/[^a-z0-9.]+/', ' ', $name) ?? $name;

        return trim(preg_replace('/\s+/', ' ', $name) ?? $name);
    }

    /**
     * Size in a comparable base unit (grams for mass, millilitres for volume).
     */
    public function size(string $name): ?string
    {
        $normalized = $this->normalize($name);

        if (! preg_match('/(\d+(?:\.\d+)?)\s*(kg|g|mg|lts|lt|ltr|litres|litre|liters|liter|ml|cl|l)\b/', $normalized, $match)) {
            return null;
        }

        $value = (float) $match[1];
        $unit = $match[2];

        return match ($unit) {
            'kg' => $this->grams($value * 1000),
            'g' => $this->grams($value),
            'mg' => $this->grams($value / 1000),
            'l', 'lt', 'ltr', 'lts', 'litre', 'litres', 'liter', 'liters' => $this->millilitres($value * 1000),
            'ml' => $this->millilitres($value),
            'cl' => $this->millilitres($value * 10),
            default => null,
        };
    }

    private function grams(float $grams): string
    {
        return round($grams, 3).'g';
    }

    private function millilitres(float $ml): string
    {
        return round($ml, 3).'ml';
    }

    private function sizeConflicts(?string $a, ?string $b): bool
    {
        if ($a === null || $b === null) {
            return false;
        }

        [$valueA, $unitA] = $this->splitSize($a);
        [$valueB, $unitB] = $this->splitSize($b);

        if ($unitA !== $unitB) {
            return true;
        }

        if ($valueA <= 0 || $valueB <= 0) {
            return false;
        }

        // Allow rounding differences (500g vs 0.5kg) but reject different packs.
        return abs($valueA - $valueB) / max($valueA, $valueB) > 0.03;
    }

    /**
     * @return array{0: float, 1: string}
     */
    private function splitSize(string $size): array
    {
        preg_match('/^([0-9.]+)(g|ml)$/', $size, $match);

        return [(float) ($match[1] ?? 0), $match[2] ?? ''];
    }

    /**
     * @param  array<int, string>  $query
     * @param  array<int, string>  $candidate
     */
    private function variantConflicts(array $query, array $candidate): bool
    {
        $queryVariants = $this->variants($query);
        $candidateVariants = $this->variants($candidate);

        // A flavour-specific pack ("Chicken", "Vanilla") is never a safe photo
        // for a listing that declares no flavour, and vice versa.
        if (($queryVariants === []) !== ($candidateVariants === [])) {
            return true;
        }

        if ($queryVariants === [] || $candidateVariants === []) {
            return false;
        }

        return array_intersect($queryVariants, $candidateVariants) === [];
    }

    /**
     * Variant words declared by a token, longest match first so "pineapple"
     * is not also recorded as "apple" and "partyjollof" resolves to "jollof".
     *
     * @param  array<int, string>  $tokens
     * @return array<int, string>
     */
    private function variants(array $tokens): array
    {
        $variants = self::VARIANT_TOKENS;
        usort($variants, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        $found = [];

        foreach ($tokens as $token) {
            foreach ($variants as $variant) {
                if ($token === $variant || (strlen($token) >= 4 && str_contains($token, $variant))) {
                    $found[$variant] = true;
                    break;
                }
            }

            // Catalogue typos: "vannina" for "vanilla".
            if (strlen($token) >= 5) {
                foreach ($variants as $variant) {
                    if ($this->tokenSimilarity($token, $variant) >= 0.85) {
                        $found[$variant] = true;
                        break;
                    }
                }
            }
        }

        return array_keys($found);
    }

    /**
     * True when both names declare a value from $map and share none.
     *
     * @param  array<int, string>  $query
     * @param  array<int, string>  $candidate
     * @param  array<string, string>  $map
     */
    private function disjoint(array $query, array $candidate, array $map): bool
    {
        $queryValues = $this->mapped($query, $map);
        $candidateValues = $this->mapped($candidate, $map);

        if ($queryValues === [] || $candidateValues === []) {
            return false;
        }

        return array_intersect($queryValues, $candidateValues) === [];
    }

    /**
     * @param  array<int, string>  $tokens
     * @param  array<string, string>  $map
     * @return array<int, string>
     */
    private function mapped(array $tokens, array $map): array
    {
        $found = [];

        foreach ($tokens as $token) {
            if (isset($map[$token])) {
                $found[$map[$token]] = true;
            }
        }

        return array_keys($found);
    }

    /**
     * @param  array<int, string>  $query
     * @param  array<int, string>  $candidate
     */
    private function oilSubtypeConflicts(array $query, array $candidate): bool
    {
        if (! in_array('oil', $query, true)) {
            return false;
        }

        $querySubtypes = $this->mapped($query, self::OIL_SUBTYPE_TOKENS);

        if ($querySubtypes === []) {
            return false;
        }

        $candidateSubtypes = $this->mapped($candidate, self::OIL_SUBTYPE_TOKENS);

        if ($candidateSubtypes === []) {
            // The source says only "cooking oil"; resolve it through the brand
            // when we know what that brand actually bottles.
            $brand = $this->brandKey($candidate);
            $inferred = $brand !== null ? (self::BRAND_OIL_SUBTYPES[$brand] ?? null) : null;

            if ($inferred === null) {
                return false;
            }

            $candidateSubtypes = [$inferred];
        }

        return array_intersect($querySubtypes, $candidateSubtypes) === [];
    }

    /**
     * Brand phrase for the oil-subtype map: "golden terra" is one brand even
     * though the catalogue writes it as two words.
     *
     * @param  array<int, string>  $tokens
     */
    private function brandKey(array $tokens): ?string
    {
        if ($tokens === []) {
            return null;
        }

        if (count($tokens) > 1) {
            $compound = $tokens[0].$tokens[1];

            if (isset(self::BRAND_OIL_SUBTYPES[$compound])) {
                return $compound;
            }
        }

        return $tokens[0];
    }

    /**
     * @param  array<int, string>  $query
     * @param  array<int, string>  $candidate
     */
    private function featureConflicts(array $query, array $candidate): bool
    {
        foreach (self::EXCLUSIVE_FEATURES as $feature) {
            $inQuery = in_array($feature, $query, true);
            $inCandidate = in_array($feature, $candidate, true);

            if ($inQuery !== $inCandidate) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sørensen–Dice coefficient over fuzzy-matched tokens.
     *
     * @param  array<int, string>  $a
     * @param  array<int, string>  $b
     */
    private function dice(array $a, array $b): float
    {
        if ($a === [] || $b === []) {
            return 0.0;
        }

        $used = [];
        $matches = 0;

        foreach ($a as $token) {
            $bestIndex = null;
            $bestSimilarity = 0.0;

            foreach ($b as $index => $candidate) {
                if (isset($used[$index])) {
                    continue;
                }

                $similarity = $this->tokenSimilarity($token, $candidate);

                if ($similarity > $bestSimilarity) {
                    $bestSimilarity = $similarity;
                    $bestIndex = $index;
                }
            }

            if ($bestIndex !== null && $bestSimilarity >= 0.85) {
                $used[$bestIndex] = true;
                $matches++;
            }
        }

        return (2 * $matches) / (count($a) + count($b));
    }

    private function tokenSimilarity(string $a, string $b): float
    {
        if ($a === $b) {
            return 1.0;
        }

        // Never fuzzy-match short tokens or size tokens: "5l" must not match "5g".
        if (strlen($a) < 4 || strlen($b) < 4) {
            return 0.0;
        }

        if (preg_match('/^\d/', $a) || preg_match('/^\d/', $b)) {
            return 0.0;
        }

        similar_text($a, $b, $percent);

        return $percent / 100;
    }

    /**
     * @param  array<int, string>  $query
     * @param  array<int, string>  $candidate
     */
    private function brandMatches(array $query, array $candidate): bool
    {
        if ($query === [] || $candidate === []) {
            return false;
        }

        $brand = $query[0];

        foreach ($candidate as $token) {
            if ($this->tokenSimilarity($brand, $token) >= 0.85) {
                return true;
            }
        }

        return false;
    }
}
