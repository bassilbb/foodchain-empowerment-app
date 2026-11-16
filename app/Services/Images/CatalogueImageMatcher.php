<?php

namespace App\Services\Images;

/**
 * Ranks a supplier product name against a downloaded catalogue and decides
 * whether the best candidate is safe enough to use as a packaging image.
 *
 * The rules are intentionally strict:
 *  - the candidate must share the brand token (first word of the supplier name);
 *  - pack size, product family, packaging form and flavour must not conflict;
 *  - the winner must beat every *different* same-brand candidate by a margin,
 *    so a product that could plausibly be two different packs is refused.
 *
 * Catalogue listings that differ only by carton/½carton wording normalise to
 * the same token set and are treated as the same product, not as ambiguity.
 */
class CatalogueImageMatcher
{
    public function __construct(private ProductNameMatcher $matcher)
    {
    }

    /**
     * @param  array<int, array{name: string, image: string, permalink?: ?string, source_site?: ?string}>  $catalogue
     * @return array<int, array<string, mixed>> scored candidates, best first
     */
    public function rank(string $query, array $catalogue): array
    {
        $scored = [];

        foreach ($catalogue as $item) {
            if (blank($item['name'] ?? null) || blank($item['image'] ?? null)) {
                continue;
            }

            $result = $this->matcher->compare($query, (string) $item['name']);
            $result['item'] = $item;
            $scored[] = $result;
        }

        usort($scored, function (array $a, array $b): int {
            return $b['score'] <=> $a['score'];
        });

        return $scored;
    }

    /**
     * Best safe candidate for one supplier product.
     *
     * @param  array<int, array{name: string, image: string, permalink?: ?string, source_site?: ?string}>  $catalogue
     * @return array{
     *     candidate: ?array<string, mixed>,
     *     score: float,
     *     margin: float,
     *     runner_up: ?string,
     *     refused: ?array<string, mixed>,
     *     accepted: bool
     * }
     */
    public function best(string $query, array $catalogue, float $minScore, float $minMargin): array
    {
        $scored = $this->rank($query, $catalogue);

        $eligible = array_values(array_filter(
            $scored,
            fn (array $row): bool => $row['brand_match'] && ! $row['conflict']
        ));

        $best = $eligible[0] ?? null;
        $refused = null;

        foreach ($scored as $row) {
            if ($row['conflict']) {
                $refused = $row;
                break;
            }
        }

        if ($best === null) {
            return [
                'candidate' => null,
                'score' => 0.0,
                'margin' => 0.0,
                'runner_up' => null,
                'refused' => $refused,
                'accepted' => false,
            ];
        }

        $second = 0.0;
        $runnerUp = null;

        foreach ($eligible as $index => $row) {
            if ($index === 0 || $this->sameListing($best, $row)) {
                continue;
            }

            if ($row['score'] > $second) {
                $second = $row['score'];
                $runnerUp = (string) $row['item']['name'];
            }
        }

        $margin = round($best['score'] - $second, 4);

        return [
            'candidate' => [
                'name' => (string) $best['item']['name'],
                'image_url' => (string) $best['item']['image'],
                'source_url' => $best['item']['permalink'] ?? null,
                'source_site' => $best['item']['source_site'] ?? null,
                'title' => (string) $best['item']['name'],
                'score' => $best['score'],
            ],
            'score' => $best['score'],
            'margin' => $margin,
            'runner_up' => $runnerUp,
            'refused' => $refused,
            'accepted' => $best['score'] >= $minScore && $margin >= $minMargin,
        ];
    }

    /**
     * Two rows describe the same catalogue listing when their normalised token
     * sets are identical (e.g. "G.P Twist 500g" and "G.P Twist 500g (Carton)").
     *
     * @param  array<string, mixed>  $a
     * @param  array<string, mixed>  $b
     */
    private function sameListing(array $a, array $b): bool
    {
        $left = $a['candidate_tokens'];
        $right = $b['candidate_tokens'];

        sort($left);
        sort($right);

        return $left === $right;
    }
}
