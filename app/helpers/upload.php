<?php
/**
 * File Upload Helper Functions
 * ────────────────────────────
 * Handles secure file uploads with validation.
 */

/**
 * Handle a file upload securely.
 *
 * @param array  $file   The $_FILES element (e.g., $_FILES['image'])
 * @param string $folder Destination folder relative to public/uploads/ (e.g., 'products')
 * @return array ['success' => bool, 'path' => string, 'error' => string]
 */
function handleUpload(array $file, string $folder = 'products'): array
{
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds the upload_max_filesize directive.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds the MAX_FILE_SIZE directive.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk.',
            UPLOAD_ERR_EXTENSION  => 'File upload stopped by extension.',
        ];
        return [
            'success' => false,
            'path'    => '',
            'error'   => $errors[$file['error']] ?? 'Unknown upload error.',
        ];
    }

    // Validate file size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return [
            'success' => false,
            'path'    => '',
            'error'   => 'File size exceeds the maximum allowed size (' . formatFileSize(UPLOAD_MAX_SIZE) . ').',
        ];
    }

    // Validate MIME type using finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES, true)) {
        return [
            'success' => false,
            'path'    => '',
            'error'   => 'Invalid file type. Allowed: ' . implode(', ', UPLOAD_ALLOWED_EXTENSIONS),
        ];
    }

    // Validate extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, UPLOAD_ALLOWED_EXTENSIONS, true)) {
        return [
            'success' => false,
            'path'    => '',
            'error'   => 'Invalid file extension. Allowed: ' . implode(', ', UPLOAD_ALLOWED_EXTENSIONS),
        ];
    }

    // Generate a UUID-based filename to prevent directory traversal and overwrites
    $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;

    // Build the destination path
    $uploadDir = dirname(__DIR__, 2) . '/public/uploads/' . basename($folder);

    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $destination = $uploadDir . '/' . $newFilename;

    // Move the uploaded file
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return [
            'success' => false,
            'path'    => '',
            'error'   => 'Failed to move uploaded file.',
        ];
    }

    // Return the relative path (stored in database)
    $relativePath = $folder . '/' . $newFilename;

    return [
        'success' => true,
        'path'    => $relativePath,
        'error'   => '',
    ];
}

/**
 * Delete an uploaded file.
 *
 * @param string $path Relative path from public/uploads/ (e.g., 'products/abc123.jpg')
 * @return bool True if deleted or file doesn't exist
 */
function deleteUpload(string $path): bool
{
    if (empty($path)) {
        return false;
    }

    // Sanitize path — prevent directory traversal
    $path = basename(dirname($path)) . '/' . basename($path);
    $fullPath = dirname(__DIR__, 2) . '/public/uploads/' . $path;

    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }

    return true; // File doesn't exist, consider it deleted
}

/**
 * Validate that a file is a valid image.
 *
 * @param array $file The $_FILES element
 * @return bool True if valid image
 */
function validateImage(array $file): bool
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES, true)) {
        return false;
    }

    // Verify it's actually an image using getimagesize
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        return false;
    }

    return true;
}

/**
 * Handle multiple file uploads.
 *
 * @param array  $files   The $_FILES element for multiple files
 * @param string $folder  Destination folder
 * @return array Array of upload results
 */
function handleMultipleUploads(array $files, string $folder = 'products'): array
{
    $results = [];

    // Normalize the $_FILES array for multiple uploads
    if (isset($files['name']) && is_array($files['name'])) {
        $fileCount = count($files['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue; // Skip empty file inputs
            }

            $singleFile = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];

            $results[] = handleUpload($singleFile, $folder);
        }
    }

    return $results;
}
