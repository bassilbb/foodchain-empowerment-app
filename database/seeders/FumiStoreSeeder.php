<?php
namespace Database\Seeders;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class FumiStoreSeeder extends Seeder {
    public function run(): void {
        $mapFile = base_path('fumi-store-images/fumi-image-map.json');
        if (! is_file($mapFile)) { $this->command?->warn('map not found'); return; }
        $map = json_decode((string) file_get_contents($mapFile), true);
        if (! is_array($map)) { $this->command?->warn('invalid map'); return; }
        $sourceDir = base_path('fumi-store-images');
        $targetDir = public_path('images/products');
        if (! is_dir($targetDir)) { @mkdir($targetDir, 0775, true); }
        $vendorUser = User::updateOrCreate(['email'=>'fumistore@foodchain.ng'], ['first_name'=>'Fumi','last_name'=>'Store','phone'=>'08099998888','password'=>bcrypt('password'),'referral_code'=>strtoupper(Str::random(8)),'is_active'=>true]);
        $vendor = Vendor::updateOrCreate(['email'=>'fumistore@foodchain.ng'], ['user_id'=>$vendorUser->id,'name'=>'Fumi Store','slug'=>'fumi-store','business_name'=>'Fumi Store','phone'=>'08099998888','city'=>'Lagos','state'=>'Lagos','description'=>'Fumi Store registered vendor.','status'=>'approved','commission_rate'=>10.0,'wallet_balance'=>0]);
        $categoryId = Category::query()->value('id');
        if (! $categoryId) { $categoryId = Category::create(['name'=>'General','slug'=>'general','is_active'=>true])->id; }
        $now = now()->toDateTimeString();
        $productIds = [];
        DB::transaction(function () use ($map,$vendor,$categoryId,$sourceDir,$targetDir,$now,&$productIds) {
            // Base prices for each Fumi Store soya oil variant (in Naira)
            $basePrices = [
                'Golden Terra Soya Oil 700ml x 12' => 1500.00,
                'Golden Terra Soya Oil 5L x 4' => 8500.00,
                'Golden Terra Soya Oil 3L x 6' => 5500.00,
                'Golden Terra Soya Oil 45ml x 96' => 500.00,
                'Golden Terra Soya Oil 25L' => 22000.00,
            ];

            foreach ($map as $entry) {
                $name = $entry['name'] ?? null; if (! $name) continue;
                $slug = $entry['slug'] ?? Str::slug($name);
                $sku = $entry['sku'] ?? ('FUMI-'.strtoupper(Str::random(8)));
                $price = $basePrices[$name] ?? 1000.00;
                $memberPrice = $price * 0.9;
                $product = Product::updateOrCreate(['slug'=>$slug], ['vendor_id'=>$vendor->id,'category_id'=>$categoryId,'brand_id'=>null,'name'=>$name,'sku'=>$sku,'unit'=>'pack','price'=>$price,'member_price'=>$memberPrice,'stock_quantity'=>50,'low_stock_threshold'=>5,'description'=>$name,'is_featured'=>false,'is_active'=>true,'approval_status'=>'approved','approved_at'=>now()]);
                $productIds[] = $product->id;
                foreach (($entry['images'] ?? []) as $img) {
                    $file = $img['file'] ?? basename($img['path'] ?? ''); if ($file==='') continue;
                    $src = $sourceDir.'/'.$file; $dst = $targetDir.'/'.$file;
                    if (is_file($src) && ! is_file($dst)) { @copy($src,$dst); }
                }
                DB::table('marketplace_product_images')->where('product_id',$product->id)->delete();
                $rows=[]; $sort=0;
                foreach (($entry['images'] ?? []) as $img) {
                    $file = $img['file'] ?? basename($img['path'] ?? ''); if ($file==='') continue;
                    $sort++;
                    $rows[] = ['product_id'=>$product->id,'image_path'=>'images/products/'.$file,'is_primary'=>(int)($img['is_primary']??0),'is_placeholder'=>0,'sort_order'=>(int)($img['sort_order']??$sort),'created_at'=>$now,'updated_at'=>$now];
                }
                if ($rows) { foreach (array_chunk($rows,500) as $chunk) { DB::table('marketplace_product_images')->insert($chunk); } }
            }
        });
        \Illuminate\Support\Facades\Artisan::call('marketplace:sync-state-pricing');
        $this->command?->info('Fumi Store seeded: '.count($productIds).' products.');
    }
}
