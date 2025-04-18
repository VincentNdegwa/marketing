<x-blog::layouts.master>
    <div id="blog-module">
        <blog-component title="Welcome to the Blog Module">
            <p>This content is coming from the Blade template.</p>
            <p>Module: {!! config('blog.name') !!}</p>
        </blog-component>
    </div>

    @vite(['Modules/Blog/Resources/js/app.ts'])
</x-blog::layouts.master>
