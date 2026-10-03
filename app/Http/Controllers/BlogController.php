<?php

namespace App\Http\Controllers;

use App\Models\PageContent;
use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * The public blog listing. Page title/subtitle stay editable through the
     * Page Content manager; the cards themselves come from the posts table.
     */
    public function index(Request $request)
    {
        if (! PageContent::isPageActive('blog')) {
            return response()->view('blog', [
                'pageTitle' => PageContent::getContent('blog', 'page_title', 'Blog'),
                'pageSubtitle' => PageContent::getContent('blog', 'page_subtitle', ''),
                'posts' => collect(),
                'disabled' => true,
            ], 403);
        }

        $query = Post::published()->with('author');

        if ($category = trim((string) $request->query('category'))) {
            $query->where('category', $category);
        }

        $posts = $query->latest('published_at')->latest('id')->paginate(9);
        $categories = Post::published()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('blog', [
            'pageTitle' => PageContent::getContent('blog', 'page_title', 'Blog'),
            'pageSubtitle' => PageContent::getContent('blog', 'page_subtitle', ''),
            'posts' => $posts,
            'categories' => $categories,
            'activeCategory' => $request->query('category'),
            'disabled' => false,
        ]);
    }

    /**
     * A single post. The slug is optional so the old /blog_details link, which
     * every legacy card pointed at, keeps working and shows the newest post.
     */
    public function show(?string $slug = null)
    {
        if (! PageContent::isPageActive('blog')) {
            return response()->view('blog_details', [
                'post' => null,
                'disabled' => true,
            ], 403);
        }

        $post = $slug
            ? Post::published()->where('slug', $slug)->first()
            : Post::published()->latest('published_at')->latest('id')->first();

        abort_if($post === null, 404);

        return view('blog_details', [
            'post' => $post,
            'related' => Post::published()
                ->whereKeyNot($post->id)
                ->latest('published_at')
                ->latest('id')
                ->limit(3)
                ->get(),
            'disabled' => false,
        ]);
    }
}
