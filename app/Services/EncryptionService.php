<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class EncryptionService
{
    /**
     * Store an encrypted file
     *
     * @param UploadedFile $file
     * @param string $disk
     * @param string $path
     * @return string Encrypted file path
     */
    public static function storeEncryptedFile(UploadedFile $file, string $disk = 'encrypted', string $path = ''): string
    {
        // Ensure encrypted directory exists
        $encryptedDir = storage_path('app/encrypted');
        if (!is_dir($encryptedDir)) {
            mkdir($encryptedDir, 0755, true);
        }
        
        // Read file contents
        $contents = file_get_contents($file->getRealPath());
        
        // Encrypt the file contents
        $encrypted = Crypt::encryptString($contents);
        
        // Generate secure filename with original extension preserved in metadata
        $originalExt = $file->getClientOriginalExtension();
        $filename = hash('sha256', $file->getClientOriginalName() . time() . uniqid()) . '.enc';
        $fullPath = $path ? $path . '/' . $filename : $filename;
        
        // Store encrypted file
        Storage::disk($disk)->put($fullPath, $encrypted);
        
        // Store original extension in a separate metadata file for later retrieval
        $metadataPath = $fullPath . '.meta';
        Storage::disk($disk)->put($metadataPath, json_encode([
            'original_name' => $file->getClientOriginalName(),
            'original_extension' => $originalExt,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]));
        
        return $fullPath;
    }

    /**
     * Retrieve and decrypt a file
     *
     * @param string $path
     * @param string $disk
     * @return string Decrypted file contents
     * @throws \Exception
     */
    public static function getDecryptedFile(string $path, string $disk = 'encrypted'): string
    {
        if (!Storage::disk($disk)->exists($path)) {
            throw new \Exception("Encrypted file not found: {$path}");
        }

        $encrypted = Storage::disk($disk)->get($path);
        
        try {
            return Crypt::decryptString($encrypted);
        } catch (\Exception $e) {
            throw new \Exception("Failed to decrypt file: " . $e->getMessage());
        }
    }

    /**
     * Get file metadata
     *
     * @param string $path
     * @param string $disk
     * @return array|null
     */
    public static function getFileMetadata(string $path, string $disk = 'encrypted'): ?array
    {
        $metadataPath = $path . '.meta';
        if (Storage::disk($disk)->exists($metadataPath)) {
            $metadata = Storage::disk($disk)->get($metadataPath);
            return json_decode($metadata, true);
        }
        return null;
    }

    /**
     * Delete an encrypted file
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public static function deleteEncryptedFile(string $path, string $disk = 'encrypted'): bool
    {
        $deleted = false;
        
        // Delete encrypted file
        if (Storage::disk($disk)->exists($path)) {
            $deleted = Storage::disk($disk)->delete($path);
        }
        
        // Delete metadata file if exists
        $metadataPath = $path . '.meta';
        if (Storage::disk($disk)->exists($metadataPath)) {
            Storage::disk($disk)->delete($metadataPath);
        }
        
        return $deleted;
    }

    /**
     * Encrypt sensitive string data
     *
     * @param string $data
     * @return string Encrypted data
     */
    public static function encryptString(string $data): string
    {
        if (empty($data)) {
            return $data;
        }
        return Crypt::encryptString($data);
    }

    /**
     * Decrypt sensitive string data
     *
     * @param string $encryptedData
     * @return string Decrypted data
     * @throws \Exception
     */
    public static function decryptString(string $encryptedData): string
    {
        if (empty($encryptedData)) {
            return $encryptedData;
        }
        
        try {
            return Crypt::decryptString($encryptedData);
        } catch (\Exception $e) {
            // If decryption fails, return original (might be unencrypted legacy data)
            return $encryptedData;
        }
    }

    /**
     * Check if a string is encrypted
     *
     * @param string $data
     * @return bool
     */
    public static function isEncrypted(string $data): bool
    {
        if (empty($data)) {
            return false;
        }
        
        // Laravel encrypted strings start with "eyJ" (base64 encoded JSON)
        return str_starts_with($data, 'eyJ');
    }
}

