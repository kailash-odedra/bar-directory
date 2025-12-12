<?php


if (!function_exists('getRouterValue')) {
    /**
     * Get router value for URL generation
     * Returns empty string for root-level deployment
     * 
     * @return string
     */
    function getRouterValue() {
        // Using root-level routing, no subdirectory needed
        return '/';
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format date consistently across admin panel
     * Format: Dec 02, 2025
     * 
     * @param mixed $date Carbon instance, DateTime, or date string
     * @param bool $includeTime Whether to include time (default: false)
     * @return string Formatted date string
     */
    function formatDate($date, $includeTime = false) {
        if (!$date) {
            return 'N/A';
        }

        try {
            if (!($date instanceof \Carbon\Carbon)) {
                $date = \Carbon\Carbon::parse($date);
            }

            if ($includeTime) {
                return $date->format('M d, Y h:i A'); // Dec 02, 2025 10:30 AM
            }

            return $date->format('M d, Y'); // Dec 02, 2025
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}