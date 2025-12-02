<?php


if (!function_exists('getRouterValue')) {
    function getRouterValue() {

        if (config('app.env') === 'production') {

            $__getRoutingValue = '/cork/laravel/modern-dark-menu/';
            
        } else if (config('app.env') === 'pre_production') {

            $__getRoutingValue = '/cork/laravel_cork_4/modern-dark-menu/';

        } else {
            
            $__getRoutingValue = '/';

        }        
        
        return $__getRoutingValue;

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