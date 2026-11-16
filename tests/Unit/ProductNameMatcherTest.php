<?php

namespace Tests\Unit;

use App\Services\Images\ProductNameMatcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The matcher decides whether a supplier name and a catalogue name describe the
 * same pack. Getting this wrong attaches a photograph of the wrong product, so
 * these cases pin down both the acceptances and the refusals.
 */
class ProductNameMatcherTest extends TestCase
{
    private ProductNameMatcher $matcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->matcher = new ProductNameMatcher;
    }

    public function test_it_recognises_an_identical_name(): void
    {
        $result = $this->matcher->compare('Dangote Sugar 1kg', 'Dangote Sugar 1kg');

        $this->assertSame(1.0, $result['score']);
        $this->assertTrue($result['brand_match']);
        $this->assertFalse($result['conflict']);
    }

    public function test_it_tolerates_typos_and_pack_count_noise(): void
    {
        // "CHCEKERS" is a transposition; "*8" is the carton count.
        $result = $this->matcher->compare('CHCEKERS CUSTARD PAP REFILL 400G*8', 'Checkers Custard Pap Refill 400g');

        $this->assertTrue($result['brand_match']);
        $this->assertFalse($result['conflict']);
        $this->assertGreaterThan(0.75, $result['score']);
    }

    public function test_it_treats_dotted_initials_as_one_token(): void
    {
        $result = $this->matcher->compare('GP SEMOVITA 2KG', 'G.P Semovita 2kg');

        $this->assertTrue($result['brand_match']);
        $this->assertFalse($result['conflict']);
        $this->assertSame(1.0, $result['score']);
    }

    public function test_it_treats_equivalent_size_units_as_equal(): void
    {
        $this->assertFalse($this->matcher->compare('Power Oil 1.4LT*8', 'Power oil 1.4L')['conflict']);
        $this->assertFalse($this->matcher->compare('Kings Veg Oil 25LT', 'Kings cooking oil 25L')['conflict']);
        $this->assertFalse($this->matcher->compare('Sugar 0.5kg', 'Sugar 500g')['conflict']);
    }

    public function test_it_refuses_a_different_pack_size(): void
    {
        $result = $this->matcher->compare('DANGOTE SUGAR 250g*40', 'Dangote Sugar 1kg');

        $this->assertTrue($result['size_conflict']);
        $this->assertTrue($result['conflict']);
    }

    public function test_it_refuses_a_different_flavour(): void
    {
        $result = $this->matcher->compare('CHECKERS CUSTARD 2KG*3 BANANA', 'Checkers Custard 2kg (Vanilla Flavour)');

        $this->assertTrue($result['variant_conflict']);
        $this->assertTrue($result['conflict']);
    }

    public function test_it_refuses_a_flavoured_pack_for_an_unflavoured_product(): void
    {
        // The supplier says only "custard"; the candidate is the banana one.
        $result = $this->matcher->compare('CHECKERS CUSTARD 400G*12', 'Checkers Custard 400g (Banana Flavor)');

        $this->assertTrue($result['variant_conflict']);
        $this->assertTrue($result['conflict']);
    }

    public function test_it_refuses_a_different_product_family(): void
    {
        $result = $this->matcher->compare('HONEYWELL WHEAT 2KG', 'Honeywell Semolina 2kg');

        $this->assertTrue($result['type_conflict']);
        $this->assertTrue($result['conflict']);
    }

    public function test_it_refuses_a_different_packaging_form(): void
    {
        $result = $this->matcher->compare('PEAK MILK 150G*24 TIN', 'Peak milk 123 (Refill) (150g)');

        $this->assertTrue($result['form_conflict']);
        $this->assertTrue($result['conflict']);
    }

    public function test_it_refuses_a_3_in_1_product_for_a_plain_one(): void
    {
        $result = $this->matcher->compare('CHECKERS CUSTARD PAP 400G*12', 'Checkers Custard (3 in 1) 400g');

        $this->assertTrue($result['feature_conflict']);
        $this->assertTrue($result['conflict']);
    }

    public function test_it_treats_extra_retail_description_words_as_the_same_product(): void
    {
        // Every supplier token is present; "milk powder refill" is description.
        $result = $this->matcher->compare('DANO FULLCREAM 2KG*6', 'Dano full cream milk powder 2kg (Refill)');

        $this->assertTrue($result['containment']);
        $this->assertSame(1.0, $result['score']);
        $this->assertFalse($result['conflict']);
    }

    public function test_containment_does_not_hide_a_missing_supplier_token(): void
    {
        // "UHT" is not in the candidate, so this is not the same product.
        $result = $this->matcher->compare('DANO UHT FULL CREAM', 'Dano full cream milk powder 2kg (Refill)');

        $this->assertFalse($result['containment']);
        $this->assertLessThan(1.0, $result['score']);
    }

    public function test_containment_ignores_a_missing_packaging_form_word(): void
    {
        $result = $this->matcher->compare('KINGS VEG OIL 1LT*12 PET', 'Kings cooking oil 1L');

        $this->assertTrue($result['containment']);
        $this->assertFalse($result['conflict']);
    }

    public function test_it_matches_compound_brand_words_written_two_ways(): void
    {
        $result = $this->matcher->compare('DANO COOLCOW 750G*12', 'Dano cool cow 750g (Refill)');

        $this->assertTrue($result['brand_match']);
        $this->assertFalse($result['conflict']);
        $this->assertGreaterThan(0.75, $result['score']);
    }

    public function test_it_infers_oil_subtype_from_a_brand_when_the_source_is_generic(): void
    {
        // Golden Terra bottles soya oil, so a generic "cooking oil" listing is
        // the right pack for a soya-oil product...
        $soya = $this->matcher->compare('GOLDEN TERRA SOYA OIL 25LT', 'Golden Terra cooking oil 25L');
        $this->assertFalse($soya['oil_conflict']);
        $this->assertFalse($soya['conflict']);

        // ...but not for a vegetable-oil product of the same brand.
        $vegetable = $this->matcher->compare('GOLDEN TERRA VEG OIL 25LT', 'Golden Terra cooking oil 25L');
        $this->assertTrue($vegetable['oil_conflict']);
        $this->assertTrue($vegetable['conflict']);
    }

    public function test_it_refuses_a_different_declared_oil_subtype(): void
    {
        $result = $this->matcher->compare('KINGS SOYA OIL 5LT', 'Kings vegetable oil 5L');

        $this->assertTrue($result['oil_conflict']);
        $this->assertTrue($result['conflict']);
    }

    public function test_power_oil_is_vegetable_not_soya(): void
    {
        // poweroil.com.ng sells "Power Oil Veg", so a generic Power listing is
        // the right pack for a vegetable product and the wrong pack for soya.
        $vegetable = $this->matcher->compare('POWER VEG OIL 1.4LT*8', 'Power oil 1.4L');
        $this->assertFalse($vegetable['oil_conflict']);

        $soya = $this->matcher->compare('POWER SOYA OIL 1.4LT*8', 'Power oil 1.4L');
        $this->assertTrue($soya['oil_conflict']);
    }

    /**
     * @return array<string, array{0: string, 1: array<int, string>}>
     */
    public static function normalisationProvider(): array
    {
        return [
            'pack count stripped' => ['MAMAGOLD SEMO 1KG*10', ['mamagold', 'semolina', '1kg']],
            'by count stripped' => ['Sugar 500g by 20', ['sugar', '500g']],
            '3 in 1 collapsed' => ['Checkers 3-in-1 400g', ['checkers', '3in1', '400g']],
            'ampersand becomes and' => ['Sugar & Salt 1kg', ['sugar', 'salt', '1kg']],
        ];
    }

    /**
     * @param  array<int, string>  $expected
     */
    #[DataProvider('normalisationProvider')]
    public function test_it_normalises_tokens(string $name, array $expected): void
    {
        $this->assertSame($expected, $this->matcher->tokens($name));
    }

    public function test_it_reads_sizes_into_a_comparable_base_unit(): void
    {
        $this->assertSame('500g', $this->matcher->size('Sugar 0.5kg'));
        $this->assertSame('2000ml', $this->matcher->size('Kings cooking oil 2L'));
        $this->assertSame('250ml', $this->matcher->size('Milk 25cl'));
        $this->assertNull($this->matcher->size('No size here'));
    }
}
