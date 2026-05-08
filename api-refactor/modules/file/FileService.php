<?php

class FileService
{
    private $fileRepository;

    public function __construct()
    {
        $this->fileRepository = new FileRepository();
    }

    public function uploadProfilePicture($currentUser, $file)
    {
        try {
            if (!$file || !isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                return [
                    'success' => false,
                    'message' => 'Please choose an image to upload'
                ];
            }

            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                return [
                    'success' => false,
                    'message' => 'File upload failed'
                ];
            }

            if ((int)($file['size'] ?? 0) > 5 * 1024 * 1024) {
                return [
                    'success' => false,
                    'message' => 'File size must be less than 5MB'
                ];
            }

            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp'
            ];

            $mimeType = mime_content_type($file['tmp_name']) ?: ($file['type'] ?? '');

            if (!isset($allowedMimeTypes[$mimeType])) {
                return [
                    'success' => false,
                    'message' => 'Please upload a valid image file'
                ];
            }

            $storageDir = dirname(__DIR__, 2) . '/storage/profile-pictures';

            if (!is_dir($storageDir) && !mkdir($storageDir, 0775, true) && !is_dir($storageDir)) {
                return [
                    'success' => false,
                    'message' => 'Failed to prepare file storage'
                ];
            }

            $extension = $allowedMimeTypes[$mimeType];
            $storedFileName = sprintf(
                'profile_%d_%s.%s',
                (int)$currentUser->id,
                bin2hex(random_bytes(8)),
                $extension
            );
            $fullPath = $storageDir . '/' . $storedFileName;

            if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
                return [
                    'success' => false,
                    'message' => 'Failed to store uploaded image'
                ];
            }

            $fileId = $this->fileRepository->create(
                $currentUser->id,
                $file['name'] ?? $storedFileName,
                $mimeType,
                (int)($file['size'] ?? 0),
                $fullPath,
                FILE_PROFILE_PICTURE
            );

            if ($fileId <= 0) {
                @unlink($fullPath);

                return [
                    'success' => false,
                    'message' => 'Failed to save uploaded file'
                ];
            }

            return [
                'success' => true,
                'message' => 'Profile picture uploaded successfully',
                'data' => [
                    'profile_picture_id' => $fileId
                ]
            ];
        } catch (Exception $e) {
            Logger::error('Profile picture upload failed', [
                'error' => $e->getMessage(),
                'user_id' => $currentUser->id ?? null
            ]);

            return [
                'success' => false,
                'message' => 'Failed to upload profile picture'
            ];
        }
    }

    public function getFileById($fileId)
    {
        try {
            if ($fileId <= 0) {
                return null;
            }

            $file = $this->fileRepository->findById($fileId);

            if (!$file) {
                return null;
            }

            $filePath = $file['file_path'] ?? '';

            if ($filePath === '' || !is_file($filePath)) {
                return null;
            }

            return $file;
        } catch (Exception $e) {
            Logger::error('Get file by id failed', [
                'error' => $e->getMessage(),
                'file_id' => $fileId
            ]);

            return null;
        }
    }
}
