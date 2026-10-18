@extends('admin.layouts.app')
@php $editing = $post->exists; @endphp
@section('title', $editing ? 'Edit Post' : 'New Post')
@section('page-title', $editing ? 'Edit Post' : 'New Post')
@section('header', $editing ? 'Edit Post' : 'New Post')
@section('subheader', 'Write the post content, image and publishing options')
@section('content')
<style>
    .post-form { margin: -5px; max-width: 1100px; }
    .post-panel {
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
        border-radius: 20px;
        padding: 18px;
        margin-bottom: 20px;
    }
    .post-panel h3 { font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px; }
    .post-layout { display: grid; grid-template-columns: minmax(0, 1fr) 300px; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .post-layout { grid-template-columns: 1fr; } }
    .form-row { margin-bottom: 14px; }
    .form-row label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; }
    .form-row input[type="text"], .form-row input[type="datetime-local"], .form-row select, .form-row textarea {
        width: 100%; border: 1px solid #cbd5e1; border-radius: 10px; padding: 10px 12px;
        font-size: 14px; background: #fff; color: #0f172a; font-family: inherit; min-width: 0;
    }
    .form-row textarea { resize: vertical; line-height: 1.55; }
    .form-row input:focus, .form-row select:focus, .form-row textarea:focus {
        outline: 2px solid rgba(16,185,129,0.35); border-color: #10b981;
    }
    .hint { font-size: 12px; color: #94a3b8; margin-top: 5px; line-height: 1.5; }
    .image-preview {
        width: 100%; height: 170px; object-fit: cover; border-radius: 14px;
        display: block; background: linear-gradient(135deg, #dbeafe, #e2e8f0);
    }
    .image-preview-fallback {
        width: 100%; height: 170px; border-radius: 14px; display: flex; align-items: center;
        justify-content: center; background: linear-gradient(135deg, #dbeafe, #e2e8f0);
        color: #94a3b8; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em;
    }
    .image-drop {
        margin-top: 10px; border: 2px dashed #cbd5e1; border-radius: 14px; padding: 16px;
        text-align: center; font-size: 13px; color: #64748b; background: rgba(248,250,252,0.6); cursor: pointer;
        transition: all 0.25s ease;
    }
    .image-drop:hover { border-color: #10b981; color: #059669; background: rgba(16,185,129,0.05); }
    .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669); border: none; color: #fff;
        padding: 12px 28px; border-radius: 12px; font-weight: 600; font-size: 14px; cursor: pointer;
        box-shadow: 0 4px 14px rgba(16,185,129,0.35); display: inline-flex; align-items: center; gap: 6px;
        transition: all 0.35s cubic-bezier(0.23,1,0.32,1);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(16,185,129,0.45); }
    .btn-secondary {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0); border: 1px solid #cbd5e1;
        color: #475569; padding: 12px 28px; border-radius: 12px; font-weight: 600; font-size: 14px;
        cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.3s ease;
    }
    .btn-secondary:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
    .btn-sm { padding: 8px 16px; font-size: 13px; }
    .alert { border-radius: 14px; padding: 16px 20px; font-weight: 500; font-size: 14px; margin-bottom: 16px; }
    .alert-success { background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.08)); border: 1px solid rgba(16,185,129,0.25); color: #065f46; }
    .alert-error { background: linear-gradient(135deg, rgba(239,68,68,0.1), rgba(220,38,38,0.08)); border: 1px solid rgba(239,68,68,0.25); color: #991b1b; }
    .checkbox-row { display: flex; align-items: center; gap: 9px; font-size: 14px; color: #334155; font-weight: 500; }
    .checkbox-row input { width: 17px; height: 17px; accent-color: #10b981; cursor: pointer; }
    .save-bar {
        position: sticky; bottom: 14px; z-index: 5; display: flex; gap: 10px; align-items: center;
        flex-wrap: wrap; background: rgba(255,255,255,0.85); backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.6); border-radius: 16px; padding: 14px 18px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    }
</style>

<div class="post-form">
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
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

    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
        <a href="{{ route('admin.posts.index') }}" class="btn-secondary btn-sm">&larr; Back to posts</a>
        @if($editing && $post->isPublished())
        <a href="{{ route('blog_details', $post->slug) }}" target="_blank" rel="noopener" class="btn-secondary btn-sm">View on site &nearr;</a>
        @endif
    </div>

    <form method="POST" action="{{ $editing ? route('admin.posts.update', $post) : route('admin.posts.store') }}" enctype="multipart/form-data">
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="post-layout">
            <div>
                <div class="post-panel">
                    <h3>Content</h3>

                    <div class="form-row">
                        <label for="title">Title <span style="color:#dc2626;">*</span></label>
                        <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required maxlength="255">
                    </div>

                    <div class="form-row">
                        <label for="slug">URL slug</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $post->slug) }}" placeholder="generated-from-the-title" maxlength="255">
                        <p class="hint">Leave blank to generate it from the title. Public link:
                            <code>/blog_details/{{ $post->slug ?: 'your-post' }}</code>
                        </p>
                    </div>

                    <div class="form-row">
                        <label for="category">Category</label>
                        <input type="text" id="category" name="category" value="{{ old('category', $post->category) }}" maxlength="100" list="category-hints">
                        <datalist id="category-hints">
                            <option value="Quality & Safety"></option>
                            <option value="Vendor Spotlight"></option>
                            <option value="Shopping Tips"></option>
                            <option value="Delivery Options"></option>
                        </datalist>
                        <p class="hint">Shown as the small uppercase label on the post card.</p>
                    </div>

                    <div class="form-row">
                        <label for="excerpt">Excerpt</label>
                        <textarea id="excerpt" name="excerpt" rows="3" maxlength="1000">{{ old('excerpt', $post->excerpt) }}</textarea>
                        <p class="hint">Short summary shown on the blog listing card.</p>
                    </div>

                    <div class="form-row" style="margin-bottom:0;">
                        <label for="body">Full body</label>
                        <textarea id="body" name="body" rows="14">{{ old('body', $post->body) }}</textarea>
                        <p class="hint">Line breaks are preserved on the post detail page.</p>
                    </div>
                </div>
            </div>

            <div>
                <div class="post-panel">
                    <h3>Post image</h3>

                    @if($post->image_url)
                        <img src="{{ $post->image_url }}" alt="{{ $post->image_alt ?: $post->title }}" class="image-preview">
                    @else
                        <div class="image-preview-fallback">No image</div>
                    @endif

                    <label for="image" class="image-drop">
                        <span id="image-picker-label">Choose a new image&hellip;</span>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp,image/avif" class="sr-only" onchange="document.getElementById('image-picker-label').textContent = this.files[0] ? this.files[0].name : 'Choose a new image…'">
                    </label>
                    <p class="hint">JPG, PNG, GIF, WEBP or AVIF up to 5&nbsp;MB. Choosing a file replaces the current image on save.</p>

                    @if($post->image)
                    <label class="checkbox-row" style="margin-top:12px;">
                        <input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))>
                        Remove current image
                    </label>
                    @endif

                    <div class="form-row" style="margin-top:14px; margin-bottom:0;">
                        <label for="image_alt">Image alt text</label>
                        <input type="text" id="image_alt" name="image_alt" value="{{ old('image_alt', $post->image_alt) }}" maxlength="255">
                        <p class="hint">Describes the image for screen readers and when it fails to load.</p>
                    </div>
                </div>

                <div class="post-panel">
                    <h3>Publishing</h3>

                    <div class="form-row">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="draft" @selected(old('status', $post->status) === 'draft')>Draft &mdash; hidden from the blog</option>
                            <option value="published" @selected(old('status', $post->status) === 'published')>Published &mdash; visible to visitors</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label for="published_at">Publish date</label>
                        <input type="datetime-local" id="published_at" name="published_at" value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
                        <p class="hint">A future date keeps the post hidden until that moment.</p>
                    </div>

                    <label class="checkbox-row">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $post->is_featured))>
                        Feature this post
                    </label>
                </div>
            </div>
        </div>

        <div class="save-bar">
            <button type="submit" class="btn-primary">{{ $editing ? 'Save changes' : 'Create post' }}</button>
            <a href="{{ route('admin.posts.index') }}" class="btn-secondary">Cancel</a>
            @if($editing)
            <span style="margin-left:auto; font-size:12px; color:#94a3b8;">
                Last updated {{ $post->updated_at ? $post->updated_at->diffForHumans() : '—' }}
            </span>
            @endif
        </div>
    </form>
</div>
@endsection
