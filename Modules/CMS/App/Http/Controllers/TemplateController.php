<?php

namespace Modules\CMS\App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\CMS\App\Models\Template;

class TemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index()
    {
        $templates = Template::with('user')
            ->withCount('pages')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return Inertia::render('cms/templates/Index', [
            'templates' => $templates
        ]);
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        return Inertia::render('cms/templates/Create');
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|json',
            'thumbnail' => 'nullable|string',
            'category' => 'nullable|string|max:100',
        ]);
        
        $template = Template::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'],
            'thumbnail' => $validated['thumbnail'] ?? null,
            'category' => $validated['category'] ?? 'General',
            'user_id' => Auth::user()->id,
        ]);
        
        if ($request->wantsJson()) {
            return response()->json($template);
        }
        
        return redirect()->route('cms.templates.index')
            ->with('success', 'Template created successfully.');
    }

    /**
     * Show the specified template.
     */
    public function show(Template $template)
    {
        return Inertia::render('cms/templates/Show', [
            'template' => $template
        ]);
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(Template $template)
    {
        return Inertia::render('cms/templates/Edit', [
            'template' => $template
        ]);
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, Template $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|json',
            'thumbnail' => 'nullable|string',
            'category' => 'nullable|string|max:100',
        ]);
        
        $template->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'],
            'thumbnail' => $validated['thumbnail'] ?? null,
            'category' => $validated['category'] ?? 'General',
        ]);
        
        if ($request->wantsJson()) {
            return response()->json($template);
        }
        
        return redirect()->route('cms.templates.index')
            ->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(Template $template)
    {
        $template->delete();
        
        return redirect()->route('cms.templates.index')
            ->with('success', 'Template deleted successfully.');
    }
    
    /**
     * Duplicate the specified template.
     */
    public function duplicate(Template $template)
    {
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (Copy)';
        $newTemplate->user_id = Auth::user()->id;
        $newTemplate->created_at = now();
        $newTemplate->updated_at = now();
        $newTemplate->save();
        
        return redirect()->route('cms.templates.index')
            ->with('success', 'Template duplicated successfully.');
    }
}
