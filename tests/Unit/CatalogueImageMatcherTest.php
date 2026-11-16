<?php

namespace Tests\Unit;

use App\Services\Images\CatalogueImageMatcher;
use App\Services\Images\ProductNameMatcher;
use PHPUnit\Framework\TestCase;

/**
 * The catalogue matcher decides what is safe to download. These cases pin down
 * the margin rule and the "duplicate listing" rule that make an automated
 * import defensible.
 */
class CatalogueImageMatcherTest extends TestCase
{
    private CatalogueImageMatcher $matcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->matcher = new CatalogueImageMatcher(new ProductNameMatcher);
    }

    /**
     * @param  array<int, string>  $names
     * @return array<int, array{name: string, image: string, permalink: string, source_site: string}>
     */
    private function catalogue(array $names): array
    {
        return array_map(fn (string $name, int $index): array => [
            'name' => $name,
            'image' => 'https://example.test/'.$index.'.png',
            'permalink' => 'https://example.test/p/'.$index,
            'source_site' => 'example.test',
        ], $names, array_keys($names));
    }

    public function test_it_accepts_a_clear_winner(): void
    {
        $result = $this->matcher->best(
            'DANGOTE SUGAR 1KG*10',
            $this->catalogue([
                'Dangote Sugar 1kg',
                'Dangote Sugar 500g',
                'Dangote Salt 1kg',
            ]),
            0.75,
            0.05
        );

        $this->assertTrue($result['accepted']);
        $this->assertSame('Dangote Sugar 1kg', $result['candidate']['name']);
        $this->assertGreaterThanOrEqual(0.05, $result['margin']);
    }

    public function test_it_treats_carton_and_half_carton_duplicates_as_one_listing(): void
    {
        // Both normalise to "gp twist 500g", so the second is not a rival.
        $result = $this->matcher->best(
            'GP TWIST 500G*20',
            $this->catalogue([
                'G.P Twist 500g (Carton)',
                'G.P Twist 500g (½Carton)',
            ]),
            0.75,
            0.05
        );

        $this->assertTrue($result['accepted']);
        $this->assertSame($result['score'], $result['margin']);
        $this->assertNull($result['runner_up']);
    }

    public function test_it_refuses_when_two_different_products_tie(): void
    {
        // Both are 350g refills of Peak, but they are different formulations.
        $result = $this->matcher->best(
            'PEAK MILK 350G*12 REFILL',
            $this->catalogue([
                'Peak milk 123 (Refill) (350g)',
                'Peak milk 456 (Refil) (350g)',
            ]),
            0.75,
            0.05
        );

        $this->assertFalse($result['accepted']);
        $this->assertNotNull($result['runner_up']);
        $this->assertLessThan(0.05, $result['margin']);
    }

    public function test_it_refuses_a_candidate_without_the_brand(): void
    {
        $result = $this->matcher->best(
            'HONEYWELL SEMOLINA 2KG',
            $this->catalogue(['Mamagold Semolina 2kg']),
            0.60,
            0.0
        );

        $this->assertFalse($result['accepted']);
        $this->assertNull($result['candidate']);
    }

    public function test_it_reports_the_best_refused_candidate_with_its_conflict(): void
    {
        $result = $this->matcher->best(
            'DANGOTE SUGAR 250g*40',
            $this->catalogue(['Dangote Sugar 1kg']),
            0.75,
            0.05
        );

        $this->assertFalse($result['accepted']);
        $this->assertNotNull($result['refused']);
        $this->assertTrue($result['refused']['size_conflict']);
    }

    public function test_it_ignores_catalogue_rows_without_an_image(): void
    {
        $result = $this->matcher->best(
            'DANGOTE SUGAR 1KG*10',
            [
                ['name' => 'Dangote Sugar 1kg', 'image' => '', 'permalink' => null, 'source_site' => null],
                ['name' => 'Dangote Sugar 1kg', 'image' => 'https://example.test/ok.png', 'permalink' => null, 'source_site' => null],
            ],
            0.75,
            0.05
        );

        $this->assertTrue($result['accepted']);
        $this->assertSame('https://example.test/ok.png', $result['candidate']['image_url']);
    }

    public function test_it_lists_candidates_best_first(): void
    {
        $ranked = $this->matcher->rank(
            'MAMADOR VEG OIL 1.5LT*6',
            $this->catalogue([
                'Mamador cooking oil 500ml',
                'Mamador cooking oil 1.5L',
                'Kings cooking oil 1.5L',
            ])
        );

        $this->assertSame('Mamador cooking oil 1.5L', $ranked[0]['item']['name']);
    }
}
