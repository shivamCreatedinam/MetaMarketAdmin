<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait ImageUploadTrait
{
    /**
     * Handle image upload and return the file path.
     *
     * @param UploadedFile $image
     * @param string $folder
     * @param string|null $filename
     * @return string
     */
    public function uploadImage(UploadedFile $image, $folder = 'images', $prefix = null, $filename = null)
    {
        // Generate a unique filename if none is provided
        $name = $filename ?: "img_{$prefix}" . mt_rand(1111, 9999) . "_" . date('dmYHis') . '.' . $image->getClientOriginalExtension();

        // Store the image in the specified folder inside the public directory
        $path = $image->storeAs($folder, $name, 'public');

        return $path;
    }

    /**
     * Delete an image from the public folder.
     *
     * @param string $path
     * @return bool
     */
    public function deleteImage($path)
    {
        return Storage::disk('public')->delete($path);
    }
}
