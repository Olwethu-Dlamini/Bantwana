<?php
// application/helpers/functions.php

/**
 * Helper function to format file size from bytes to a human-readable string.
 * @param int|null $bytes File size in bytes. Can be null.
 * @param int $decimals Number of decimal places.
 * @return string Formatted file size string (e.g., "1.50 MB"). Returns '0 Bytes' for null/0.
 */
function formatFileSize($bytes, $decimals = 2) {
    // Handle null or zero bytes explicitly
    if ($bytes === null || $bytes === 0) {
        return '0 Bytes';
    }

    $k = 1024;
    // Ensure $bytes is a positive number before log calculations
    if ($bytes < 0) {
        return 'Invalid Size';
    }
    // Calculate the appropriate unit index
    $logResult = log($bytes) / log($k);
    // Prevent division by zero or negative log results causing issues
    if (!is_finite($logResult) || $logResult < 0) {
         return '0 Bytes'; // Fallback for invalid/non-positive values
    }
    $i = floor($logResult);

    // Ensure $i doesn't exceed the array bounds
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = min($i, count($sizes) - 1);

    // Calculate and format the size
    $calculatedSize = $bytes / pow($k, $i);
    // Ensure $calculatedSize is a finite number
    if (!is_finite($calculatedSize)) {
        return '0 Bytes'; // Fallback for calculation errors
    }
    return round($calculatedSize, $decimals) . ' ' . $sizes[$i];
}

// You can add other general-purpose helper functions here later
?>