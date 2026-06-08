<?php

/**
 * Location Helper
 * File: app/Helpers/location_helper.php
 * Atau: app/Modules/GuestBook/Helpers/location_helper.php
 */

if (!function_exists('cookie_location')) {
    /**
     * Get location data from cookie
     * 
     * @return object|null
     */
    function cookie_location()
    {
        $locationId = get_cookie('location_id');
        
        if (empty($locationId)) {
            return null;
        }
        
        // Get location data from database
        $location = get_ref_single('locations', 'ID="' . $locationId . '"', 'data');
        
        return $location;
    }
}

if (!function_exists('has_location_cookie')) {
    /**
     * Check if location cookie exists and valid
     * 
     * @return bool
     */
    function has_location_cookie()
    {
        $locationId = get_cookie('location_id');
        
        if (empty($locationId)) {
            return false;
        }
        
        // Verify location exists in database
        $location = get_ref_single('locations', 'ID="' . $locationId . '"', 'data');
        
        return !empty($location);
    }
}

if (!function_exists('get_location_cookie')) {
    /**
     * Get specific location cookie value
     * 
     * @param string $key (id, name, code)
     * @return string|null
     */
    function get_location_cookie($key = 'id')
    {
        $cookieName = 'location_' . $key;
        return get_cookie($cookieName);
    }
}

if (!function_exists('set_location_cookies')) {
    /**
     * Set location cookies
     * 
     * @param object $location
     * @param int $expire (in days, default 30)
     * @return bool
     */
    function set_location_cookies($location, $expire = 30)
    {
        try {
            $cookieOptions = [
                'expires'  => time() + ($expire * 24 * 60 * 60),
                'path'     => '/',
                'secure'   => false, // Set true untuk HTTPS
                'httponly' => true,
                'samesite' => 'Lax'
            ];
            
            $result1 = set_cookie('location_id', $location->ID, $cookieOptions);
            $result2 = set_cookie('location_name', $location->Name, $cookieOptions);
            $result3 = set_cookie('location_code', $location->Code, $cookieOptions);
            
            return $result1 && $result2 && $result3;
        } catch (Exception $e) {
            log_message('error', 'Failed to set location cookies: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('clear_location_cookies')) {
    /**
     * Clear all location cookies
     * 
     * @return bool
     */
    function clear_location_cookies()
    {
        try {
            $result1 = delete_cookie('location_id');
            $result2 = delete_cookie('location_name');
            $result3 = delete_cookie('location_code');
            
            return $result1 && $result2 && $result3;
        } catch (Exception $e) {
            log_message('error', 'Failed to clear location cookies: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('get_location_info')) {
    /**
     * Get complete location information for display
     * 
     * @return array
     */
    function get_location_info()
    {
        $locationId = get_cookie('location_id');
        
        if (empty($locationId)) {
            return [
                'status' => false,
                'message' => 'Lokasi belum diset',
                'data' => null
            ];
        }
        
        $location = get_ref_single('locations', 'ID="' . $locationId . '"', 'data');
        
        if (empty($location)) {
            return [
                'status' => false,
                'message' => 'Lokasi tidak valid',
                'data' => null
            ];
        }
        
        return [
            'status' => true,
            'message' => 'Lokasi aktif',
            'data' => $location
        ];
    }
}

if (!function_exists('require_location_cookie')) {
    /**
     * Middleware-like function to ensure location cookie exists
     * Redirect to set-location if not exists
     * 
     * @param string $redirectUrl (optional intended URL after setting location)
     * @return \CodeIgniter\HTTP\RedirectResponse|null
     */
    function require_location_cookie($redirectUrl = null)
    {
        if (!has_location_cookie()) {
            $session = session();
            
            if ($redirectUrl) {
                $session->set('intended_url', $redirectUrl);
            }
            
            return redirect()->to('guestbook/set-location');
        }
        
        return null;
    }
}

if (!function_exists('validate_location_code')) {
    /**
     * Validate location code against database
     * 
     * @param string $code
     * @return object|false
     */
    function validate_location_code($code)
    {
        if (empty($code)) {
            return false;
        }
        
        $location = get_ref_single('locations', 'UPPER(Code)="' . strtoupper($code) . '"', 'data');
        
        return $location ?: false;
    }
}

if (!function_exists('get_current_location')) {
    /**
     * Get current active location from cookie
     * 
     * @return object|null
     */
    function get_current_location()
    {
        $locationId = get_cookie('location_id');
        
        if (empty($locationId)) {
            return null;
        }
        
        return get_ref_single('locations', 'ID="' . $locationId . '"', 'data');
    }
}