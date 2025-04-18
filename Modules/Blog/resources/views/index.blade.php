<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog Module</title>
    @vite(['Modules/Blog/Resources/js/app.ts'], 'build-blog')
</head>
<body>
    <div id="blog-module">
        <blog-component title="Welcome to the Blog Module">
            <p>This content is coming from the Blade template.</p>
            <p>Module: {!! config('blog.name') !!}</p>
        </blog-component>
    </div>
</body>
</html>
