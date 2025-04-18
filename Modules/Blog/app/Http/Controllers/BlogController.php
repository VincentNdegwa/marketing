<?php

namespace Modules\Blog\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // No need for constructor anymore as the root view is set in the service provider
    
    public function index()
    {
        $posts = [
            [
                'id' => 1,
                'title' => 'Getting Started with Laravel Modules',
                'excerpt' => 'Learn how to organize your Laravel application using modules...',
                'image' => null,
                'created_at' => '2025-04-15T14:30:00',
                'updated_at' => '2025-04-15T14:30:00'
            ],
            [
                'id' => 2,
                'title' => 'Integrating Vue.js with Laravel Modules',
                'excerpt' => 'A comprehensive guide to using Vue.js in your modular Laravel app...',
                'image' => null,
                'created_at' => '2025-04-16T10:15:00',
                'updated_at' => '2025-04-16T10:15:00'
            ],
            [
                'id' => 3,
                'title' => 'Building Scalable Applications with Laravel',
                'excerpt' => 'Best practices for creating maintainable and scalable Laravel applications...',
                'image' => null,
                'created_at' => '2025-04-17T09:45:00',
                'updated_at' => '2025-04-17T09:45:00'
            ]
        ];
        
        // Now render the Vue component - this is just the component name, not a Blade view path
        return Inertia::module('blog/Index', [
            'posts' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::module('blog/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) 
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);
        
        // In a real app, store the blog post in the database
        // For now, just redirect back with a success message
        
        return redirect()->route('blog.index')
            ->with('success', 'Blog post created successfully!');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        // Sample post data (in a real app, fetch from database)
        $post = [
            'id' => $id,
            'title' => 'Getting Started with Laravel Modules',
            'content' => '<p>Laravel Modules is a package by Nicolas Widart that allows you to organize your Laravel application into modules. This makes it easier to maintain and scale your application as it grows.</p><h2>Why Use Laravel Modules?</h2><p>As your Laravel application grows, it can become difficult to maintain. By organizing your code into modules, you can:</p><ul><li>Keep related code together</li><li>Make your codebase more maintainable</li><li>Reuse modules across different projects</li><li>Develop and test modules independently</li></ul>',
            'excerpt' => 'Learn how to organize your Laravel application using modules...',
            'image' => null,
            'created_at' => '2025-04-15T14:30:00',
            'updated_at' => '2025-04-15T14:30:00'
        ];
        
        return Inertia::render('Blog/Show', [
            'post' => $post
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Sample post data (in a real app, fetch from database)
        $post = [
            'id' => $id,
            'title' => 'Getting Started with Laravel Modules',
            'content' => 'Laravel Modules is a package by Nicolas Widart that allows you to organize your Laravel application into modules. This makes it easier to maintain and scale your application as it grows.',
            'excerpt' => 'Learn how to organize your Laravel application using modules...',
            'image' => null,
            'created_at' => '2025-04-15T14:30:00',
            'updated_at' => '2025-04-15T14:30:00'
        ];
        
        return Inertia::render('Blog/Edit', [
            'post' => $post
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) 
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);
        
        // In a real app, update the blog post in the database
        // For now, just redirect back with a success message
        
        return redirect()->route('blog.show', $id)
            ->with('success', 'Blog post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) 
    {
        // In a real app, delete the blog post from the database
        // For now, just redirect back with a success message
        
        return redirect()->route('blog.index')
            ->with('success', 'Blog post deleted successfully!');
    }
}
