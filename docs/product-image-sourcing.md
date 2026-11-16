# Product image sourcing

How outstanding marketplace products get a **genuine** packaging image, and why
some of them are deliberately left without one.

## The rule

An image is only ever attached when it comes from a real listing that can be
pointed at. Nothing is generated, invented or guessed. When the evidence is not
strong enough the product keeps its placeholder and the candidate is left for a
human in the existing image-research screen.

## Two ways to source

| Provider | `IMAGE_SEARCH_PROVIDER` | What it does |
| --- | --- | --- |
| Search engine | `serpapi` / `bing` | Calls an approved image-search API. Needs a key. |
| Catalogue | `catalogue` | Reads a supplier's own public WooCommerce Store API. No key. |
| None | *(unset)* | Manual mode: rows are recorded as `pending_research`. |

The catalogue provider exists because a supplier price list is full of
wholesale names (`CHECKERS CUSTARD PAP REFILL 400G*8`) that no image search
engine matches reliably, while the supplier's own storefront sells the same
packs under shopper-facing names (`Checkers Custard Pap Refill 400g`). Matching
against that catalogue gives a name, a permalink and an image URL that a person
can check.

## Commands

```bash
# 1. Snapshot the supplier storefront (names, permalinks, image URLs).
php artisan images:sync-catalogue
#    --url= --path= --per-page=100 --max-pages=50

# 2. See what would happen. Writes and downloads nothing.
php artisan images:import-from-site --dry-run

# 3. Apply: auto-approve strong matches, download, attach.
php artisan images:import-from-site
#    --vendor= --limit= --sku= --min-score= --min-margin= --no-download
```

`images:sync-catalogue` reads
`{base_url}/wp-json/wc/store/products?per_page=100&page=N`. The target site's
`robots.txt` allows product pages and uploads and disallows only cart and
`wp-admin` URLs, none of which are requested.

The import command only considers research rows whose product has **no real
image**, so it is safe to re-run. Rows that were verified but whose download
failed are retried on the next run.

## How a match is decided

`ProductNameMatcher` normalises both names and then applies hard rules before
any score is considered:

1. **Brand** — the first token of the supplier name must fuzzy-match a token in
   the candidate (`CHCEKERS` → `Checkers`, `COOLCOW` → `cool cow`).
2. **Pack size** — extracted to a base unit (grams or millilitres). A 250 g
   listing is never accepted for a 1 kg product, and `5L` equals `5LT`.
3. **Product family** — wheat is not semolina, spaghetti is not spaghettini,
   custard is not milk.
4. **Packaging form** — a `Tin` is not a `Refill`.
5. **Flavour / variant** — a banana pack is not a vanilla pack, and a flavoured
   pack is not a safe photo for a product that declares no flavour.
6. **Exclusive features** — a `3 in 1` sachet is not the plain pap refill.
7. **Oil subtype** — where the source only says "cooking oil", the brand decides
   what it is. A soya-oil product is therefore refused a vegetable-oil pack
   shot, and vice versa. Only brands confirmed by the manufacturer or a major
   retailer are mapped:

   | Brand | Oil | Evidence |
   | --- | --- | --- |
   | Golden Terra / Terra | soya | goldenterra.com — "Golden Terra Soya Oil" |
   | Grand | soya | grandcereals.com — "Grand Pure Soya Oil" |
   | Power | vegetable | poweroil.com.ng — "Power Oil Veg" |
   | Mamador | vegetable | mamador.com.ng — "Mamador Pure Vegetable Oil" |
   | Kings | vegetable | devonkings.com.ng, Supermart, Jumia — "Kings Vegetable Oil" |
   | Emapoa | palm | Emapoa Palm Oil (Konga; the supplier's own listing) |

   A brand must **not** be added on assumption: a wrong entry either accepts the
   wrong pack shot or refuses a correct one. This is why the map is short.

Anything that survives those rules is scored with the Sørensen–Dice coefficient
over fuzzy-matched tokens. When every identity token of the supplier name is
present in the catalogue name, the extra words are treated as retail
description and the score is 1.0 — `DANO FULLCREAM 2KG*6` matches
`Dano full cream milk powder 2kg (Refill)`. Packaging-form words (`PET`,
`BOTTLE`) are ignored on the supplier side, and a generic `cooking oil`
candidate is credited with its brand's confirmed subtype (see rule 7).
`CatalogueImageMatcher` then requires:

* score **≥ 0.75** (`IMAGE_CATALOGUE_MIN_SCORE`), and
* a lead of **≥ 0.05** (`IMAGE_CATALOGUE_MIN_MARGIN`) over the best *different*
  same-brand listing.

Listings that normalise to the same tokens — `G.P Twist 500g (Carton)` and
`G.P Twist 500g (½Carton)` — count as one product, not as ambiguity. Two
different products at the same size (Peak `123` vs `456` 350 g refills) are
genuine ambiguity and stay in review.

Scores between **0.60** (`IMAGE_CATALOGUE_REVIEW_SCORE`) and 0.75 are stored as
`pending_verification` with their confidence so an administrator can decide.
Below 0.60 nothing is stored, to keep the review queue free of noise.

### Resolution floor

Manual uploads keep the 250×250 minimum. Catalogue pack shots are often
published small, and the name/size/variant match has already established what
the pack is, so the import uses a lower floor (`IMAGE_CATALOGUE_MIN_WIDTH` /
`IMAGE_CATALOGUE_MIN_HEIGHT`, default 100×100). Images are still validated as
real images, converted to WebP and written to `public/images/products`.

## Audit trail

Every attempt is recorded on `marketplace_product_image_research`:

| Column | Meaning |
| --- | --- |
| `status` | `pending_research`, `pending_verification`, `verified`, `downloaded`, `rejected`, `not_found` |
| `candidate_confidence` | Match score (0–1) |
| `candidate_match_method` | `catalogue-name-match` or `provider-score` |
| `candidate_title` / `candidate_image_url` / `candidate_source_url` | What was proposed and where it came from |
| `download_status` / `download_error` | Whether the file arrived, and why not |
| `verified_by` / `verified_at` / `reviewer_notes` | Who approved it and on what basis |

## Results for Opeyemi Foods (vendor 17)

Run on 2026-11-16 against `https://obaspalacefoods.com.ng`.

| | |
| --- | --- |
| Products audited | 564 |
| Catalogue listings synced | 506 (store reports 525; 19 have no image) |
| Auto-matched and attached | **62** |
| Queued for review | 2 |
| No acceptable candidate | 500 |

All 62 attached images are `is_placeholder = false`, `is_primary = true`, and
their files exist under `public/images/products/opeyemi-*.webp`. A first pass
downloaded 30 and rejected 28 for being under 250 px; after applying the
catalogue resolution floor all 28 downloaded successfully. Re-running the import
is a no-op for these products because rows whose product already has a real image
are skipped.

The source was verified as exhausted: the Store API reports 525 products and the
`product-sitemap.xml` lists the same 525. The 19 products the API returns
without an image also have no `og:image` on their product pages, so there is no
second source of images on this site.

### Images shared across pack sizes

The source storefront sometimes publishes a **single** pack shot for several
sizes of the same product. 13 of the 62 attachments therefore share an image
with a sibling pack size — for example `checkers.png` is listed by the supplier
for both the 2 kg and 400 g Checkers custard, and `mamagold-semo.png` for every
Mamagold semolina size. These are genuine brand images from the supplier's own
listings, but the label in the photo may show a different pack size than the
row it is attached to. This is a limitation of the source, not of the matcher.

### Left in review (2)

| SKU | Product | Why |
| --- | --- | --- |
| OPEY-0126 | DANO COOLCOW 750G*12+10PCS CHOCO | Promo bundle; `choco` is not in the refill listing |
| OPEY-0479 | PEAK MILK 350G*12 REFILL | Tied between Peak `123` and `456` (margin 0.000) |

### Why 500 stay unmatched

The supplier's wholesale list covers brands and pack formats the storefront does
not sell. 452 of the 500 have no same-brand, conflict-free candidate at all.
Typical examples:

* Wholesale-only sizes — `DANGOTE SUGAR 100G*100`, `BOW SOYA OIL 1LTR*12` when
  the site lists only the 1 kg bag or the 5 L bottle.
* Different naming that still conflicts on a hard rule — `GOLDEN TERRA VEG OIL
  25LT` against a soya-oil listing, `CHIKKI CHICKEN NOODLES 100G` against the
  70 g pack.
* Brands with no listing on the site at all.

Every one of the 106 brand-matched refusals was inspected: each is a different
size, product family, packaging form, flavour or oil subtype. Only 5 candidates
scored in the 0.50–0.55 band, and all 5 are different products. Refusing these
is the point: a 1 kg sugar bag is not a photograph of a 100 g sachet.

Going further needs a **second catalogue source** (another supplier that stocks
these brands/sizes) or an approved **image-search API key**; the current
storefront cannot supply them.

## Tests

```bash
php artisan test tests/Unit/ProductNameMatcherTest.php
php artisan test tests/Unit/CatalogueImageMatcherTest.php
php artisan test tests/Feature/Marketplace/CatalogueImageImportTest.php
php artisan test tests/Feature/Marketplace/ImageResearchWorkflowTest.php
```

37 new tests cover token normalisation, every conflict rule, the margin and
duplicate-listing rules, containment scoring, the evidence-backed brand→oil map,
catalogue syncing with HTML entities, dry runs, auto-attachment, review queuing,
retry-after-failure and the guarantee that a product which already has a real
image is never touched. Together with the existing image workflow tests the
suite is 46 tests / 159 assertions.
