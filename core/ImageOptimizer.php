<?php

namespace Core;

class ImageOptimizer {
    
    /**
     * Process an uploaded image: resize if needed and convert to WebP.
     * 
     * @param string $sourcePath The temporary uploaded file path (e.g. $_FILES['tmp_name'])
     * @param string $destinationPath The final destination path (should end with .webp)
     * @param int $maxWidth The maximum width allowed
     * @param int $maxHeight The maximum height allowed
     * @param int $quality The WebP quality (0-100)
     * @return bool True on success, false on failure
     */
    public static function processUpload($sourcePath, $destinationPath, $maxWidth = 1920, $maxHeight = 1080, $quality = 85) {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $info = getimagesize($sourcePath);
        if ($info === false) {
            return false;
        }

        $width = $info[0];
        $height = $info[1];
        $mime = $info['mime'];

        // Calculate new dimensions
        $newWidth = $width;
        $newHeight = $height;

        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = $width / $height;
            $maxRatio = $maxWidth / $maxHeight;

            if ($ratio > $maxRatio) {
                // Width is the limiting factor
                $newWidth = $maxWidth;
                $newHeight = (int)($maxWidth / $ratio);
            } else {
                // Height is the limiting factor
                $newHeight = $maxHeight;
                $newWidth = (int)($maxHeight * $ratio);
            }
        }

        // Load image based on mime type
        $image = null;
        switch ($mime) {
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $image = @imagecreatefromwebp($sourcePath);
                break;
            default:
                return false; // Unsupported format
        }

        if (!$image) {
            return false;
        }

        // Create new image canvas
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF (even though WebP handles transparency, we need to preserve it in GD first)
        if ($mime == 'image/png' || $mime == 'image/gif' || $mime == 'image/webp') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Resize and resample
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save as WebP
        $result = imagewebp($newImage, $destinationPath, $quality);

        // Free memory
        imagedestroy($image);
        imagedestroy($newImage);

        return $result;
    }
}
