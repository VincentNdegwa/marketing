<?php

namespace Modules\CMS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\CMS\app\Models\Page;
use Modules\CMS\app\Models\Template;

class PageController extends Controller
{
    /**
     * Display a listing of the pages.
     */
    public function index()
    {
        $pages = Page::with('user')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return Inertia::module('cms/pages/Index', [
            'pages' => $pages
        ]);
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        $templates = Template::select('id', 'name', 'description', 'thumbnail')
            ->orderBy('name')
            ->get();
            
        return Inertia::module('cms/grapejs/Editor', [
            'page' => null,
            'templates' => $templates
        ]);
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'description' => 'nullable|string|max:1000',
            'content' => 'required|array',
            'status' => 'required|in:draft,published',
            'template_id' => 'nullable|exists:templates,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:255',
        ]);
        
        $validated['user_id'] = Auth::id();
        
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }
        
        $page = Page::create($validated);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Page created successfully',
                'page' => $page
            ]);
        }
        
        return redirect()
            ->route('cms.pages.edit', $page->id)
            ->with('success', 'Page created successfully.');
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Page $page)
    {
        $templates = Template::select('id', 'name', 'description', 'thumbnail')
            ->orderBy('name')
            ->get();
            
        return Inertia::module('cms/grapejs/Editor', [
            'page' => $page,
            'templates' => $templates
        ]);
    }

    /**
     * Update the specified page in storage.
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'description' => 'nullable|string|max:1000',
            'content' => 'required|array',
            'status' => 'required|in:draft,published',
            'template_id' => 'nullable|exists:templates,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:255',
        ]);
        
        // Set published_at if status changed to published
        if ($validated['status'] === 'published' && $page->status !== 'published') {
            $validated['published_at'] = now();
        }
        
        $page->update($validated);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Page updated successfully',
                'page' => $page
            ]);
        }
        
        return redirect()
            ->route('cms.pages.edit', $page->id)
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Preview the specified page.
     */
    public function preview(Page $page)
    {
        // Here you could render a special preview version
        // For now, we'll just return the page data
        return view('cms::preview', [
            'page' => $page
        ]);
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();
        
        return redirect()
            ->route('cms.pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}
