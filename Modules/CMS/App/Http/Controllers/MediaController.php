<?php

namespace Modules\CMS\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\CMS\App\Models\Media;
use Inertia\Inertia;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $folder = $request->input('folder', '/');
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);
        
        // Get media files for the current folder
        $query = Media::where('folder', $folder);
        
        // Apply search if provided
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%")
                  ->orWhere('caption', 'like', "%{$search}%");
            });
        }
        
        // Get paginated results
        $files = $query->orderBy('created_at', 'desc')
                      ->paginate($perPage, ['*'], 'page', $page);
        
        // Get subfolders for the current folder
        $folders = $this->getFolders($folder);
        
        if ($request->wantsJson()) {
            return response()->json([
                'files' => $files->items(),
                'folders' => $folders,
                'total' => $files->total(),
                'current_page' => $files->currentPage(),
                'per_page' => $files->perPage(),
                'last_page' => $files->lastPage(),
            ]);
        }
        
        return Inertia::render('cms/media/Index', [
            'files' => $files->items(),
            'folders' => $folders,
            'total' => $files->total(),
            'current_page' => $files->currentPage(),
            'per_page' => $files->perPage(),
            'last_page' => $files->lastPage(),
        ]);
    }

    /**
     * Upload media files
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'folder' => 'nullable|string',
        ]);
        
        $folder = $request->input('folder', '/');
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();
        
        // Generate a unique file name to prevent overwriting
        $uniqueFileName = Str::uuid() . '_' . $fileName;
        
        // Store the file
        $path = $file->storeAs('public/media' . $folder, $uniqueFileName);
        
        // Create media record
        $media = Media::create([
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'file_name' => $fileName,
            'file_path' => str_replace('public/', '', $path),
            'mime_type' => $mimeType,
            'size' => $fileSize,
            'folder' => $folder,
            'user_id' => Auth::user()->id,
        ]);
        
        return response()->json($media);
    }

    /**
     * Delete a media file
     *
     * @param  \Modules\CMS\App\Models\Media  $media
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Media $media)
    {
        // Delete the file from storage
        Storage::delete('public/' . $media->file_path);
        
        // Delete the record
        $media->delete();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Create a new folder
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent' => 'nullable|string',
        ]);
        
        $name = $request->input('name');
        $parent = $request->input('parent', '/');
        
        // Sanitize folder name
        $name = Str::slug($name);
        
        // Create the folder path
        $folderPath = rtrim($parent, '/') . '/' . $name;
        
        // Create the folder in storage
        Storage::makeDirectory('public/media' . $folderPath);
        
        return response()->json([
            'success' => true,
            'path' => $folderPath,
        ]);
    }
    
    /**
     * Delete a folder
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $folder
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFolder(Request $request, $folder)
    {
        // Delete all media files in this folder
        Media::where('folder', $folder)->delete();
        
        // Delete the folder from storage
        Storage::deleteDirectory('public/media' . $folder);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Update media file details
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Modules\CMS\App\Models\Media  $media
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Media $media)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
        ]);
        
        $media->update([
            'name' => $request->input('name'),
            'alt_text' => $request->input('alt_text'),
            'caption' => $request->input('caption'),
            'metadata' => $request->input('metadata', []),
        ]);
        
        return response()->json([
            'success' => true,
            'media' => $media
        ]);
    }
    
    /**
     * Get subfolders for a given folder
     *
     * @param  string  $path
     * @return array
     */
    private function getFolders($path)
    {
        $directories = Storage::directories('public/media' . $path);
        $folders = [];
        
        foreach ($directories as $directory) {
            $name = basename($directory);
            $relativePath = str_replace('public/media', '', $directory);
            $count = Media::where('folder', $relativePath)->count();
            
            $folders[] = [
                'name' => $name,
                'path' => $relativePath,
                'count' => $count,
            ];
        }
        
        return $folders;
    }
}
