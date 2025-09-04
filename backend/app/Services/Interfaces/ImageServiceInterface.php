<?php

namespace App\Services\Interfaces;

interface ImageServiceInterface
{
    /**
     * Upload and process an image
     */
    public function uploadImage($file, string $directory = 'images'): array;

    /**
     * Upload user avatar
     */
    public function uploadUserAvatar($file, int $userId): array;

    /**
     * Upload admin avatar
     */
    public function uploadAdminAvatar($file, int $adminId): array;

    /**
     * Upload membership package image
     */
    public function uploadMembershipImage($file, int $packageId): array;

    /**
     * Upload image for any entity
     */
    public function uploadEntityImage($file, string $entityType, int $entityId, string $imageType = 'images'): array;

    /**
     * Resize image to specific dimensions
     */
    public function resizeImage(string $imagePath, int $width, int $height, string $quality = 'high'): string;

    /**
     * Generate thumbnail
     */
    public function generateThumbnail(string $imagePath, int $width = 150, int $height = 150): string;

    /**
     * Delete image and its variations
     */
    public function deleteImage(string $imagePath): bool;

    /**
     * Get image URL
     */
    public function getImageUrl(string $imagePath): string;

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrl(string $imagePath): string;

    // /**
    //  * Validate image file
    //  */
    // public function validateImage($file, array $rules = []): array;

    /**
     * Optimize image for web
     */
    public function optimizeImage(string $imagePath): string;

    /**
     * Convert image format
     */
    public function convertImage(string $imagePath, string $format): string;
}
