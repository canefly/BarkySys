<?php
function resolveUploadPath($relativePath) {
    $cleanPath = ltrim($relativePath, '/');
    return realpath(__DIR__ . '/../' . $cleanPath);
}
