<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $status = $request->query('status');

        $posts = Post::with('author')
            ->when($search, fn ($q) => $q->where(fn ($w) => $w
                ->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%")))
            ->when(in_array($status, Post::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->latest('published_at')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $counts = [
            'all' => Post::count(),
            'published' => Post::published()->count(),
            'drafts' => Post::drafts()->count(),
        ];

        return view('admin.posts.index', compact('posts', 'search', 'status', 'counts'));
    }

    public function create()
    {
        $post = new Post([
            'status' => 'draft',
            'published_at' => now(),
        ]);

        return view('admin.posts.form', compact('post'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['author_id'] = auth()->guard('admin')->id();
        $data['is_featured'] = $request->boolean('is_featured');

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeImage($request->file('image'));
        }

        $post = Post::create($data);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', "“{$post->title}” has been created.");
    }

    public function edit(Post $post)
    {
        $post->load('author');

        return view('admin.posts.form', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validated($request, $post);

        // A blank slug field means "leave the URL alone", not "rebuild it".
        // Otherwise every title edit would silently break published links.
        if (blank($data['slug']) && filled($post->slug)) {
            unset($data['slug']);
        }

        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $this->deleteImageFile($post);
            $data['image'] = $this->storeImage($request->file('image'));
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImageFile($post);
            $data['image'] = null;
        }

        // Publishing something that was a draft should default a publish date.
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post->update($data);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', "“{$post->title}” has been updated.");
    }

    public function destroy(Post $post)
    {
        $title = $post->title;

        $this->deleteImageFile($post);
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', "“{$title}” has been deleted.");
    }

    /**
     * Returns every managed field explicitly. $request->validate() only echoes
     * back keys that were actually submitted, which makes optional fields
     * disappear from the array and leaves them unfillable on create.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Post $post = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($post?->id)],
            'category' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(Post::STATUSES)],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp,avif', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        // ConvertEmptyStringsToNull turns a cleared textarea into null, so an
        // omitted key and an emptied field are both deliberate "no value".
        return [
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? null,
            'category' => $validated['category'] ?? null,
            'excerpt' => $validated['excerpt'] ?? null,
            'body' => $validated['body'] ?? null,
            'image_alt' => $validated['image_alt'] ?? null,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? null,
        ];
    }

    private function storeImage($file): string
    {
        return $file->store('blog', 'public');
    }

    /**
     * Only removes files this app owns. The four imported posts originally
     * pointed at public/assets artwork, so a legacy path is left on disk.
     */
    private function deleteImageFile(Post $post): void
    {
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }
    }
}
