<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Illuminate\Support\Str;

class ImageService
{
    protected ImageManager $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * Generate and store a resized photo.
     *
     * @param \Illuminate\Http\UploadedFile $uploadedFile
     * @param int $width
     * @param int $height
     * @param string $baseFolder
     * @return string
     */
    public function uploadResizedPhotoToStorage($uploadedFile, int $width = 70, int $height = 70, string $baseFolder = 'default'): string
    {
        // Generate a unique file name
        $fileName = Str::random(10) . '.jpg';

        // Form the full path to the file
        $directory = storage_path("app/public/{$baseFolder}");
        $filePath = "{$directory}/{$fileName}";

        // Ensure the directory exists
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Temporarily save the uploaded file
        $tempFile = tmpfile();
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];
        copy($uploadedFile->getRealPath(), $tempFilePath);

        // Load the image for processing
        $image = $this->imageManager->read($tempFilePath);

        // Determine the size of the crop (smallest side to ensure a square)
        $cropSize = min($image->width(), $image->height());

        // Crop the image from the center
        $image->crop($cropSize, $cropSize, ($image->width() - $cropSize) / 2, ($image->height() - $cropSize) / 2);

        // Resize the image to the specified dimensions
        $image->resize($width, $height);

        // Save the processed image
        $image->save($filePath);

        fclose($tempFile);

        // Return the path to the resized image
        return "storage/{$baseFolder}/{$fileName}";
    }
}