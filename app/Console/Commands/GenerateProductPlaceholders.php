<?php

namespace App\Console\Commands;

use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductImage;
use App\Support\ProductPlaceholder;
use Illuminate\Console\Command;

class GenerateProductPlaceholders extends Command
{
    protected $signature = 'marketplace:placeholders';

    protected $description = 'Generate static placeholder images for products that do not have real photos';

    public function handle(): int
    {
        $generated = 0;

        Product::with(['images', 'category'])->chunkById(100, function ($products) use (&$generated) {
            foreach ($products as $product) {
                if ($product->hasRealImages()) {
                    continue;
                }

                $category = optional($product->category)->slug;
                $placeholders = $product->images->where('is_placeholder', true);

                if ($placeholders->isEmpty()) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => ProductPlaceholder::store($product->slug, $product->name, $category),
                        'is_primary' => true,
                        'is_placeholder' => true,
                        'sort_order' => 0,
                    ]);
                    $generated++;

                    continue;
                }

                foreach ($placeholders->values() as $index => $image) {
                    $slug = $index === 0 ? $product->slug : $product->slug.'-'.($index + 1);
                    $image->image_path = ProductPlaceholder::store($slug, $product->name, $category);
                    $image->save();
                    $generated++;
                }
            }
        });

        $this->info("Generated {$generated} placeholder image(s).");

        return self::SUCCESS;
    }
}
