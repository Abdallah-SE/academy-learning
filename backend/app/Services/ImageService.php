<?php

namespace App\Services;

use App\Services\Interfaces\ImageServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class ImageService implements ImageServiceInterface
{
    /**
     * Upload and process an image
     */
    public function uploadImage($file, string $directory = 'images'): array
    {
        try {
            if (!$file || !$file instanceof UploadedFile) {
                return ['success' => false, 'message' => 'Invalid file provided'];
            }

            $fileName = $this->generateFileName($file);
            $filePath = $directory . '/' . $fileName;

            $stored = Storage::disk('public')->put($filePath, file_get_contents($file));

            if (!$stored) {
                return ['success' => false, 'message' => 'Failed to store image'];
            }

            return [
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => [
                    'path' => $filePath,
                    'url' => $this->getImageUrl($filePath),
                    'filename' => $fileName,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]
            ];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Image upload failed: ' . $e->getMessage()];
        }
    }

    /**
     * Upload user avatar
     */
    public function uploadUserAvatar($file, int $userId): array
    {
        return $this->uploadImage($file, "users/{$userId}/avatars");
    }

    /**
     * Upload admin avatar
     */
    public function uploadAdminAvatar($file, int $adminId): array
    {
        return $this->uploadImage($file, "admins/{$adminId}/avatars");
    }

    /**
     * Upload membership package image
     */
    public function uploadMembershipImage($file, int $packageId): array
    {
        return $this->uploadImage($file, "memberships/{$packageId}/images");
    }

    /**
     * Upload image for any entity
     */
    public function uploadEntityImage($file, string $entityType, int $entityId, string $imageType = 'images'): array
    {
        return $this->uploadImage($file, "{$entityType}/{$entityId}/{$imageType}");
    }

    /**
     * Resize image to specific dimensions
     */
    public function resizeImage(string $imagePath, int $width, int $height, string $quality = 'high'): string
    {
        // Implementation would use Intervention Image
        return $imagePath;
    }

    /**
     * Generate thumbnail
     */
    public function generateThumbnail(string $imagePath, int $width = 150, int $height = 150): string
    {
        // Implementation would use Intervention Image
        return $imagePath;
    }

    /**
     * Delete image and its variations
     */
    public function deleteImage(string $imagePath): bool
    {
        try {
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get image URL
     */
    public function getImageUrl(string $imagePath): string
    {
        return Storage::disk('public')->url($imagePath);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrl(string $imagePath): string
    {
        return $this->getImageUrl($imagePath);
    }

    /**
     * Optimize image for web
     */
    public function optimizeImage(string $imagePath): string
    {
        return $imagePath;
    }

    /**
     * Convert image format
     */
    public function convertImage(string $imagePath, string $format): string
    {
        return $imagePath;
    }

    /**
     * Generate unique filename
     */
    protected function generateFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return Str::random(40) . '.' . $extension;
    }
}
