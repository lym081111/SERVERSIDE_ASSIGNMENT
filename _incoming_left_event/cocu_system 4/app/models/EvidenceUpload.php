<?php

class EvidenceUpload {

    private const MAX_SIZE_BYTES = 5242880; // 5 MB

    private const MIME_TO_EXTENSION = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    public static function uploadFromRequest($fieldName = 'evidence_file') {
        if (!isset($_FILES[$fieldName])) {
            return [
                'uploaded' => false,
                'path' => null,
                'error' => null,
            ];
        }

        $file = $_FILES[$fieldName];
        $errorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($errorCode === UPLOAD_ERR_NO_FILE) {
            return [
                'uploaded' => false,
                'path' => null,
                'error' => null,
            ];
        }

        if ($errorCode !== UPLOAD_ERR_OK) {
            return [
                'uploaded' => false,
                'path' => null,
                'error' => 'File upload failed. Please try again.',
            ];
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);
        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            return [
                'uploaded' => false,
                'path' => null,
                'error' => 'Uploaded file could not be validated.',
            ];
        }

        if ($size <= 0 || $size > self::MAX_SIZE_BYTES) {
            return [
                'uploaded' => false,
                'path' => null,
                'error' => 'Proof document must be between 1 byte and 5 MB.',
            ];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($tmpPath);
        if (!isset(self::MIME_TO_EXTENSION[$mime])) {
            return [
                'uploaded' => false,
                'path' => null,
                'error' => 'Only PDF, JPG, and PNG files are allowed.',
            ];
        }

        $uploadDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'evidence';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            return [
                'uploaded' => false,
                'path' => null,
                'error' => 'Unable to prepare upload folder.',
            ];
        }

        try {
            $fileToken = bin2hex(random_bytes(8));
        } catch (Exception $e) {
            $fileToken = str_replace('.', '', uniqid('', true));
        }
        $filename = 'evidence_' . date('Ymd_His') . '_' . $fileToken . '.' . self::MIME_TO_EXTENSION[$mime];
        $destination = $uploadDir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($tmpPath, $destination)) {
            return [
                'uploaded' => false,
                'path' => null,
                'error' => 'Unable to save uploaded file.',
            ];
        }

        return [
            'uploaded' => true,
            'path' => 'uploads/evidence/' . $filename,
            'error' => null,
        ];
    }
}

?>
