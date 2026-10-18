@extends('admin.layouts.app')
@section('title', 'Blog Posts')
@section('page-title', 'Blog Posts')
@section('header', 'Blog Posts')
@section('subheader', 'Create, edit and publish blog articles')
@section('content')
<style>
    .posts-page { margin: -5px; }
    .post-card {
        position: relative;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(255,255,255,0.6);
        box-shadow:
            0 1px 3px rgba(0,0,0,0.04),
            0 8px 32px rgba(0,0,0,0.06),
            0 24px 64px rgba(0,0,0,0.04),
            inset 0 1px 0 rgba(255,255,255,0.8);
        transform: perspective(1200px) rotateX(0deg) translateZ(0);
        transition: all 0.45s cubic-bezier(0.23,1,0.32,1);
        border-radius: 20px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .post-card:hover {
        transform: perspective(1200px) rotateX(1.5deg) translateZ(12px) translateY(-3px);
        background: rgba(255,255,255,0.95);
        box-shadow:
            0 1px 3px rgba(0,0,0,0.04),
            0 12px 48px rgba(0,0,0,0.08),
            0 32px 80px rgba(0,0,0,0.06);
    }
    .post-thumb {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 14px;
        display: block;
        background: linear-gradient(135deg, #dbeafe, #e2e8f0);
    }
    .post-thumb-fallback {
        width: 100%;
        height: 150px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #dbeafe, #e2e8f0);
        color: #94a3b8;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .post-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin: 12px 0 6px;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }
    .post-excerpt {
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        overflow-wrap: anywhere;
    }
    .post-meta { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; font-size: 11px; color: #94a3b8; }
    .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none; color: #fff; padding: 12px 28px; border-radius: 12px;
        font-weight: 600; font-size: 14px; cursor: pointer;
        box-shadow: 0 4px 14px rgba(16,185,129,0.35);
        display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.35s cubic-bezier(0.23,1,0.32,1);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(16,185,129,0.45); }
    .btn-secondary {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        border: 1px solid #cbd5e1; color: #475569; padding: 12px 28px;
        border-radius: 12px; font-weight: 600; font-size: 14px; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px; transition: all 0.3s ease;
    }
    .btn-secondary:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
    .btn-sm { padding: 8px 16px; font-size: 13px; }
    .btn-danger { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
    .btn-danger:hover { background: #fee2e2; box-shadow: 0 4px 16px rgba(239,68,68,0.18); }
    .badge { padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; }
    .badge-published { background: rgba(16,185,129,0.12); color: #059669; border: 1px solid rgba(16,185,129,0.2); }
    .badge-draft { background: rgba(245,158,11,0.12); color: #b45309; border: 1px solid rgba(245,158,11,0.22); }
    .badge-featured { background: rgba(139,92,246,0.12); color: #7c3aed; border: 1px solid rgba(139,92,246,0.2); }
    .alert { border-radius: 14px; padding: 16px 20px; font-weight: 500; font-size: 14px; margin-bottom: 16px; }
    .alert-success { background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.08)); border: 1px solid rgba(16,185,129,0.25); color: #065f46; }
    .alert-error { background: linear-gradient(135deg, rgba(239,68,68,0.1), rgba(220,38,38,0.08)); border: 1px solid rgba(239,68,68,0.25); color: #991b1b; }
    .filter-bar { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 18px; }
    .filter-bar input, .filter-bar select {
        border: 1px solid #cbd5e1; border-radius: 10px; padding: 10px 14px;
        font-size: 14px; background: #fff; color: #334155; min-width: 0;
    }
    .filter-bar input:focus, .filter-bar select:focus { outline: 2px solid rgba(16,185,129,0.35); border-color: #10b981; }
    .posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr)); gap: 20px; }
    .empty-state { text-align: center; padding: 3rem 1.5rem; color: #94a3b8; }
    .pagination-note { margin-top: 18px; font-size: 13px; color: #64748b; }
</style>

<div class="posts-page">
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-error">
        <ul style="margin:0; padding-left:18px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:18px;">
        <div>
            <h2 style="font-size:20px; font-weight:700; color:#1e293b; margin:0;">Blog Posts</h2>
            <p style="font-size:13px; color:#64748b; margin:4px 0 0;">
                {{ $counts['all'] }} total &middot; {{ $counts['published'] }} published &middot; {{ $counts['drafts'] }} {{ $counts['drafts'] === 1 ? 'draft' : 'drafts' }}
            </p>
        </div>
        <a href="{{ route('admin.posts.create') }}" class="btn-primary">+ New Post</a>
    </div>

    <form method="GET" action="{{ route('admin.posts.index') }}" class="filter-bar">
        <input type="search" name="q" value="{{ $search }}" placeholder="Search title, excerpt or category…" aria-label="Search posts">
        <select name="status" aria-label="Filter by status">
            <option value="">All statuses</option>
            <option value="published" @selected($status === 'published')>Published</option>
            <option value="draft" @selected($status === 'draft')>Drafts</option>
        </select>
        <button type="submit" class="btn-secondary btn-sm">Filter</button>
        @if($search || $status)
        <a href="{{ route('admin.posts.index') }}" class="btn-secondary btn-sm">Reset</a>
        @endif
    </form>

    <div class="posts-grid">
        @forelse($posts as $post)
        <article class="post-card">
            @if($post->image_url)
                <img src="{{ $post->image_url }}" alt="{{ $post->image_alt ?: $post->title }}" class="post-thumb" loading="lazy">
            @else
                <div class="post-thumb-fallback">No image</div>
            @endif

            <div class="post-meta">
                <span class="badge badge-{{ $post->status === 'published' ? 'published' : 'draft' }}">{{ $post->status }}</span>
                @if($post->is_featured)
                <span class="badge badge-featured">Featured</span>
                @endif
                @if($post->category)
                <span class="badge" style="background:rgba(59,130,246,0.10); color:#2563eb; border:1px solid rgba(59,130,246,0.2);">{{ $post->category }}</span>
                @endif
            </div>

            <h3 class="post-title">{{ $post->title }}</h3>
            @if($post->excerpt)
            <p class="post-excerpt">{{ $post->excerpt }}</p>
            @endif

            <div class="post-meta" style="margin-top:auto; padding-top:10px;">
                <span>{{ $post->published_at ? $post->published_at->format('j M Y') : 'Unscheduled' }}</span>
                @if($post->author)
                <span>&middot; {{ $post->author->name }}</span>
                @endif
            </div>

            <div style="display:flex; gap:8px; margin-top:12px; flex-wrap:wrap;">
                <a href="{{ route('admin.posts.edit', $post) }}" class="btn-primary btn-sm">Edit</a>
                @if($post->isPublished())
                <a href="{{ route('blog_details', $post->slug) }}" target="_blank" rel="noopener" class="btn-secondary btn-sm">View</a>
                @endif
                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post? This removes it from the public blog.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-secondary btn-sm btn-danger">Delete</button>
                </form>
            </div>
        </article>
        @empty
        <div class="empty-state" style="grid-column: 1 / -1;">
            @if($search || $status)
            No posts match this filter. <a href="{{ route('admin.posts.index') }}">Clear it</a>.
            @else
            No blog posts yet. <a href="{{ route('admin.posts.create') }}">Create the first post</a>.
            @endif
        </div>
        @endforelse
    </div>

    @if($posts->hasPages())
    <div class="pagination-note">{{ $posts->onEachSide(1)->links() }}</div>
    @endif
</div>
@endsection
