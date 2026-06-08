<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('baca-ditempat', ['namespace' => 'ReadOnSpot\Controllers'], function ($subroutes) {
    // Main pages
    $subroutes->add('', 'ReadOnSpot::index');
    $subroutes->add('index', 'ReadOnSpot::index');
    
    // AJAX endpoints - sesuai dengan method yang sudah dibuat
    $subroutes->post('addByMemberNumber', 'ReadOnSpot::addByMemberNumber');  // Search member
    $subroutes->post('addByBarcode', 'ReadOnSpot::addByBarcode');            // Add book via barcode  
    $subroutes->get('getTodayData', 'ReadOnSpot::getTodayData');             // Get today's data
    $subroutes->post('resetForm', 'ReadOnSpot::resetForm');                  // Reset form
    
    // Additional endpoints for reports and management
    $subroutes->get('statistics', 'ReadOnSpot::getStatistics');
    $subroutes->post('markAsReturned', 'ReadOnSpot::markAsReturned');        // Mark book as returned
    $subroutes->get('export', 'ReadOnSpot::exportData');
    
    // Keep existing routes for backward compatibility (optional)
    $subroutes->get('search-member', 'ReadOnSpot::addByMemberNumber');       // Alias
    $subroutes->get('search-book', 'ReadOnSpot::searchBook');                // Additional search
    $subroutes->post('store', 'ReadOnSpot::store');                          // Manual store
    $subroutes->post('auto-save', 'ReadOnSpot::addByBarcode');               // Alias untuk auto-save
    $subroutes->get('recent-data', 'ReadOnSpot::getTodayData');              // Alias
    $subroutes->post('set-return', 'ReadOnSpot::markAsReturned');            // Alias
    $subroutes->get('active-readers', 'ReadOnSpot::getActiveReaders');
    $subroutes->get('popular-books', 'ReadOnSpot::getPopularBooks');
});