<?php

/**
 * Image Validation Helper
 * 
 * Provides safe image handling for PDF generation and HTML rendering.
 * Validates that image files exist before loading them into PDFs to prevent
 * "ERROR n°6 : Impossible to load the image" errors.
 * 
 * @package     CodeIgniter
 * @subpackage  Helpers
 * @category    Helpers
 * @author      Development Team
 */

/**
 * Get Safe Image Path
 * 
 * Validates if an image file exists at the given path.
 * If the file exists, returns the path for use in img src attributes.
 * If the file is missing, null, empty, or invalid, returns empty string to prevent errors.
 * 
 * @param   string  $image_path     The image file path (relative or absolute)
 * @param   string  $base_path      Optional base path (usually FCPATH for filesystem images)
 * @return  string  The validated image path, or empty string if file doesn't exist
 */
function get_safe_image_path($image_path = '', $base_path = '')
{
    // Handle null, empty, or whitespace-only paths
    if (empty($image_path) || !is_string($image_path)) {
        return '';
    }

    // Trim whitespace
    $image_path = trim($image_path);

    // If still empty after trim
    if (empty($image_path)) {
        return '';
    }

    // If no base path provided, use FCPATH (filesystem path constant)
    if (empty($base_path)) {
        $base_path = defined('FCPATH') ? FCPATH : '';
    }

    // Build the full filesystem path for verification
    $full_path = $base_path . $image_path;

    // Normalize path (remove duplicate slashes, etc.)
    $full_path = str_replace('\\', '/', $full_path);

    // Check if the file exists and is readable
    if (file_exists($full_path) && is_file($full_path) && is_readable($full_path)) {
        // File exists - return the original path for use in img src
        return $image_path;
    }

    // File doesn't exist or isn't readable - return empty string
    // This prevents HTML2PDF/TCPDF from throwing "ERROR n°6"
    return '';
}

/**
 * Get Image With Fallback
 * 
 * Similar to get_safe_image_path, but can return a fallback path/URL if image doesn't exist.
 * Useful for web display where you might want to show a placeholder image.
 * 
 * @param   string  $image_path     The primary image file path
 * @param   string  $fallback_path  Optional fallback path if primary doesn't exist
 * @param   string  $base_path      Optional base path
 * @return  string  Either the primary path, fallback path, or empty string
 */
function get_image_with_fallback($image_path = '', $fallback_path = '', $base_path = '')
{
    // First try the primary image path
    $primary = get_safe_image_path($image_path, $base_path);

    if (!empty($primary)) {
        return $primary;
    }

    // Primary failed, try fallback if provided
    if (!empty($fallback_path)) {
        $fallback = get_safe_image_path($fallback_path, $base_path);
        if (!empty($fallback)) {
            return $fallback;
        }
    }

    // Both failed - return empty string
    return '';
}

/**
 * Image Exists Safe
 * 
 * Simple boolean check to see if an image file exists and is accessible.
 * 
 * @param   string  $image_path     The image file path
 * @param   string  $base_path      Optional base path
 * @return  bool    TRUE if file exists and is readable, FALSE otherwise
 */
function image_exists_safe($image_path = '', $base_path = '')
{
    if (empty($image_path) || !is_string($image_path)) {
        return FALSE;
    }

    $image_path = trim($image_path);

    if (empty($image_path)) {
        return FALSE;
    }

    if (empty($base_path)) {
        $base_path = defined('FCPATH') ? FCPATH : '';
    }

    $full_path = $base_path . $image_path;
    $full_path = str_replace('\\', '/', $full_path);

    return (file_exists($full_path) && is_file($full_path) && is_readable($full_path));
}

/**
 * Get Image With Default Fallback
 * 
 * Professional approach for PDF generation: Always returns a valid image path.
 * Tries the primary image first, falls back to default image if not found.
 * This ensures images always render visually instead of being skipped.
 * 
 * **RECOMMENDED FOR PDF GENERATION**
 * 
 * @param   string  $image_path         The primary image file path
 * @param   string  $default_image      The default fallback image path (e.g., 'uploads/default/no-image.png')
 * @param   string  $base_path          Optional base path (defaults to FCPATH)
 * @return  string  The primary image path, default image path, or empty string
 */
function get_image_with_default($image_path = '', $default_image = '', $base_path = '')
{
    // Validate primary image path
    if (!empty($image_path)) {
        $primary = get_safe_image_path($image_path, $base_path);
        if (!empty($primary)) {
            return $primary;  // Primary image exists - use it
        }
    }

    // Primary failed or missing - try default fallback
    if (!empty($default_image)) {
        $fallback = get_safe_image_path($default_image, $base_path);
        if (!empty($fallback)) {
            return $fallback;  // Default image exists - use it
        }
    }

    // Both failed - return empty string (this prevents HTML2PDF from breaking on the img src)
    return '';
}

/**
 * Get Image Display HTML
 * 
 * Creates safe img tag HTML. If image doesn't exist, returns placeholder text instead.
 * Useful for HTML rendering where you want a fallback display.
 * For PDFs, use get_image_with_default() instead which prevents HTML2PDF errors entirely.
 * 
 * @param   string  $image_path     The image file path
 * @param   string  $alt_text       Alt text for the image
 * @param   string  $attributes     Additional HTML attributes for img tag
 * @param   string  $placeholder    Text to display if image not found
 * @param   string  $base_path      Optional base path
 * @return  string  Either img tag HTML or placeholder text
 */
function get_image_html($image_path = '', $alt_text = 'Image', $attributes = '', $placeholder = '', $base_path = '')
{
    // Check if image exists
    if (image_exists_safe($image_path, $base_path)) {
        // Image exists - return img tag
        $html = '<img src="' . $image_path . '" alt="' . htmlspecialchars($alt_text) . '"';
        if (!empty($attributes)) {
            $html .= ' ' . $attributes;
        }
        $html .= '>';
        return $html;
    }

    // Image doesn't exist - return placeholder if provided
    if (!empty($placeholder)) {
        return '<div style="color: #999; font-size: 12px; padding: 10px; text-align: center; border: 1px dashed #ccc; background: #f9f9f9;">'
            . htmlspecialchars($placeholder)
            . '</div>';
    }

    // No placeholder - return empty string (for PDFs, this is safe; for HTML, nothing displays)
    return '';
}
