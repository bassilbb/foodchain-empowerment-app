<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageContentController extends Controller
{
    public function index()
    {
        $sections = PageContent::orderBy('page')->orderBy('sort_order')->get()->groupBy('page');
        $pages = $sections->keys();

        return view('admin.page-contents.index', compact('pages', 'sections'));
    }

    public function edit(string $page)
    {
        $sections = PageContent::where('page', $page)->orderBy('sort_order')->get();

        if ($sections->isEmpty()) {
            return redirect()->route('admin.page-contents.index')->with('error', 'Page not found.');
        }

        return view('admin.page-contents.edit', compact('page', 'sections'));
    }

    public function update(Request $request, string $page)
    {
        $sections = PageContent::where('page', $page)->orderBy('sort_order')->get();

        foreach ($sections as $section) {
            if ($section->type === 'image') {
                if ($request->hasFile("sections.{$section->id}")) {
                    $request->validate([
                        "sections.{$section->id}" => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
                    ]);
                    $file = $request->file("sections.{$section->id}");
                    $path = $file->store("page-contents/{$page}", 'public');
                    if ($section->value) {
                        Storage::disk('public')->delete($section->value);
                    }
                    $section->update(['value' => $path]);
                }
            } elseif ($section->type === 'boolean') {
                $section->update(['value' => $request->boolean("sections.{$section->id}") ? 'true' : 'false']);
            } elseif ($section->type === 'textarea') {
                $section->update(['value' => $request->input("sections.{$section->id}")]);
            } else {
                $section->update(['value' => $request->input("sections.{$section->id}")]);
            }
        }

        return redirect()->route('admin.page-contents.edit', $page)
            ->with('success', "{$page} page content updated successfully.");
    }

    public function seed()
    {
        $pages = [
            'landing' => [
                ['section' => 'hero_title', 'label' => 'Hero Title', 'type' => 'text', 'value' => 'Join the FoodChain Community', 'sort_order' => 1],
                ['section' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'text', 'value' => 'Empowering communities through food assistance and sustainable support programs.', 'sort_order' => 2],
                ['section' => 'hero_image', 'label' => 'Hero Image', 'type' => 'image', 'value' => null, 'sort_order' => 3],
                ['section' => 'about_title', 'label' => 'About Title', 'type' => 'text', 'value' => 'About FoodChain', 'sort_order' => 4],
                ['section' => 'about_content', 'label' => 'About Content', 'type' => 'textarea', 'value' => 'FoodChain Community Empowerment Initiative is dedicated to eradicating hunger and empowering communities through sustainable food programs.', 'sort_order' => 5],
                ['section' => 'stats_members', 'label' => 'Members Count Text', 'type' => 'text', 'value' => 'Active Members', 'sort_order' => 6],
                ['section' => 'stats_distributions', 'label' => 'Distributions Count Text', 'type' => 'text', 'value' => 'Food Distributions', 'sort_order' => 7],
                ['section' => 'stats_communities', 'label' => 'Communities Count Text', 'type' => 'text', 'value' => 'Communities Reached', 'sort_order' => 8],
                ['section' => 'cta_title', 'label' => 'CTA Title', 'type' => 'text', 'value' => 'Ready to Make a Difference?', 'sort_order' => 9],
                ['section' => 'cta_content', 'label' => 'CTA Content', 'type' => 'textarea', 'value' => 'Join thousands of members already benefiting from FoodChain community programs.', 'sort_order' => 10],
            ],
            'gallery' => [
                ['section' => 'page_title', 'label' => 'Page Title', 'type' => 'text', 'value' => 'Our Gallery', 'sort_order' => 1],
                ['section' => 'page_subtitle', 'label' => 'Page Subtitle', 'type' => 'textarea', 'value' => 'Moments from our community programs and events.', 'sort_order' => 2],
            ],
            'team' => [
                ['section' => 'page_title', 'label' => 'Page Title', 'type' => 'text', 'value' => 'Our Team', 'sort_order' => 1],
                ['section' => 'page_subtitle', 'label' => 'Page Subtitle', 'type' => 'textarea', 'value' => 'Meet the dedicated people behind FoodChain.', 'sort_order' => 2],
            ],
            'sponsor' => [
                ['section' => 'page_title', 'label' => 'Page Title', 'type' => 'text', 'value' => 'Become a Sponsor', 'sort_order' => 1],
                ['section' => 'page_subtitle', 'label' => 'Page Subtitle', 'type' => 'textarea', 'value' => 'Partner with us to make a lasting impact.', 'sort_order' => 2],
            ],
            'supplier' => [
                ['section' => 'page_title', 'label' => 'Page Title', 'type' => 'text', 'value' => 'Register as a Supplier', 'sort_order' => 1],
                ['section' => 'page_subtitle', 'label' => 'Page Subtitle', 'type' => 'textarea', 'value' => 'Partner with FoodChain as a supplier.', 'sort_order' => 2],
            ],
            'rider' => [
                ['section' => 'page_title', 'label' => 'Page Title', 'type' => 'text', 'value' => 'Become a Dispatch Rider', 'sort_order' => 1],
                ['section' => 'page_subtitle', 'label' => 'Page Subtitle', 'type' => 'textarea', 'value' => 'Join our delivery network.', 'sort_order' => 2],
            ],
            'blog' => [
                ['section' => 'page_title', 'label' => 'Page Title', 'type' => 'text', 'value' => 'Our Blog', 'sort_order' => 1],
                ['section' => 'page_subtitle', 'label' => 'Page Subtitle', 'type' => 'textarea', 'value' => 'Latest news and updates.', 'sort_order' => 2],
            ],
        ];

        foreach ($pages as $page => $sections) {
            foreach ($sections as $section) {
                PageContent::updateOrCreate(
                    ['page' => $page, 'section' => $section['section']],
                    $section
                );
            }
        }

        return redirect()->route('admin.page-contents.index')
            ->with('success', 'Default page content created successfully.');
    }

    public function toggle(string $page)
    {
        $sections = PageContent::where('page', $page)->get();

        if ($sections->isEmpty()) {
            return redirect()->route('admin.page-contents.index')->with('error', 'Page not found.');
        }

        $anyActive = $sections->contains(fn ($s) => $s->is_active);
        $newActive = ! $anyActive;

        PageContent::where('page', $page)->update(['is_active' => $newActive]);

        $status = $newActive ? 'enabled' : 'disabled';

        return redirect()->route('admin.page-contents.index')
            ->with('success', "{$page} page {$status} successfully.");
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'page' => 'required|string',
            'section_id' => 'required|integer',
        ]);

        $path = $request->file('image')->store("page-contents/{$request->page}", 'public');

        $content = PageContent::where('id', $request->section_id)->where('page', $request->page)->firstOrFail();
        if ($content->value) {
            Storage::disk('public')->delete($content->value);
        }
        $content->update(['value' => $path]);

        return response()->json(['path' => asset("storage/{$path}")]);
    }
}
