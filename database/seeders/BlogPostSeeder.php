<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Moves the four blog cards that used to be hard-coded in
 * resources/views/blog.blade.php into the posts table, so switching the blog
 * over to the database did not lose any of the existing content.
 *
 * Each image is copied out of public/ into the public disk so the image column
 * references a managed file an admin can later replace, instead of a baked-in
 * asset() path. Safe to run repeatedly: existing slugs are skipped.
 */
class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $posts = [
            [
                'title' => 'How We Keep Your Groceries Quality, Safe and Approved',
                'slug' => 'how-we-keep-your-groceries-quality-safe-and-approved',
                'category' => 'Quality & Safety',
                'excerpt' => 'From clean, well-processed staples to NAFDAC-approved products, discover how we hold every item on the marketplace to a high quality standard.',
                'image_alt' => 'Quality Groceries',
                'source' => 'assets/landing/images/blogsa.jpeg',
            ],
            [
                'title' => 'From Local Stall to a Growing Marketplace Store',
                'slug' => 'from-local-stall-to-nationwide-marketplace-store',
                'category' => 'Vendor Spotlight',
                'excerpt' => 'Discover how grassroots food businesses grew their reach using the marketplace, landing orders from customers in new locations every single week.',
                'image_alt' => 'Local Vendor Stories',
                'source' => 'assets/landing/images/blogpost.jpeg',
            ],
            [
                'title' => 'Getting Better Grocery Value With Loyalty Points',
                'slug' => 'getting-better-grocery-value-with-loyalty-points',
                'category' => 'Shopping Tips',
                'excerpt' => 'Learn how to build points on everyday orders, spot flash deals, and redeem rewards to lower the real cost of your monthly food shop.',
                'image_alt' => 'Shopping Tips',
                'source' => 'assets/landing/images/about-uss.webp',
            ],
            [
                'title' => 'Home, Pickup or Delivery: Which is Right for You?',
                'slug' => 'home-pickup-or-community-delivery',
                'category' => 'Delivery Options',
                'excerpt' => 'Compare every delivery option on the marketplace and choose the one that fits your schedule, budget, and neighborhood.',
                'image_alt' => 'Fresh Delivery',
                'source' => 'assets/landing/images/blogf.jpeg',
            ],
        ];

        $storageDir = storage_path('app/public/blog');

        foreach ($posts as $position => $post) {
            if (DB::table('posts')->where('slug', $post['slug'])->exists()) {
                continue;
            }

            $image = null;
            $source = public_path($post['source']);

            if (is_file($source)) {
                File::ensureDirectoryExists($storageDir);
                $filename = $post['slug'] . '.' . pathinfo($post['source'], PATHINFO_EXTENSION);

                if (@copy($source, $storageDir . DIRECTORY_SEPARATOR . $filename)) {
                    $image = 'blog/' . $filename;
                }
            }

            DB::table('posts')->insert([
                'title' => $post['title'],
                'slug' => $post['slug'],
                'category' => $post['category'],
                'excerpt' => $post['excerpt'],
                'body' => null,
                'image' => $image,
                'image_alt' => $post['image_alt'],
                'status' => 'published',
                'is_featured' => false,
                'published_at' => $now->copy()->subDays(($position + 1) * 9),
                'author_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
