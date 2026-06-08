<?php
// File: app/Helpers/message_helper.php
// Helper untuk menampilkan pesan flash

if (!function_exists('get_message')) {
    /**
     * Menampilkan pesan flash dengan Bootstrap alert
     * 
     * @param string $key Session key untuk pesan
     * @return string HTML alert
     */
    function get_message($key = 'message')
    {
        $session = session();
        $message = $session->getFlashdata($key);
        
        if (!$message) {
            return '';
        }
        
        // Jika message adalah array dengan type dan text
        if (is_array($message)) {
            $type = $message['type'] ?? 'info';
            $text = $message['text'] ?? '';
        } else {
            // Jika message adalah string biasa
            $type = 'info';
            $text = $message;
        }
        
        // Map type ke Bootstrap alert class
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            'danger' => 'alert-danger'
        ];
        
        // Map type ke icon
        $alertIcon = [
            'success' => 'fa-check-circle',
            'error' => 'fa-exclamation-triangle',
            'warning' => 'fa-exclamation-triangle',
            'info' => 'fa-info-circle',
            'danger' => 'fa-exclamation-triangle'
        ];
        
        $class = $alertClass[$type] ?? 'alert-info';
        $icon = $alertIcon[$type] ?? 'fa-info-circle';
        
        return "
        <div class='alert {$class} alert-dismissible fade show' role='alert'>
            <i class='fa {$icon} mr-2'></i>
            <strong>" . ucfirst($type) . "!</strong> {$text}
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
            </button>
        </div>";
    }
}

if (!function_exists('set_message')) {
    /**
     * Set pesan flash
     * 
     * @param string $type Type pesan (success, error, warning, info)
     * @param string $text Isi pesan
     * @param string $key Session key
     */
    function set_message($type, $text, $key = 'message')
    {
        $session = session();
        $session->setFlashdata($key, [
            'type' => $type,
            'text' => $text
        ]);
    }
}

if (!function_exists('show_validation_errors')) {
    /**
     * Menampilkan validation errors dalam format Bootstrap alert
     * 
     * @param array $errors Array errors dari validation
     * @return string HTML alert
     */
    function show_validation_errors($errors = [])
    {
        if (empty($errors)) {
            return '';
        }
        
        $html = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>";
        $html .= "<i class='fa fa-exclamation-triangle mr-2'></i>";
        $html .= "<strong>Validation Error!</strong> Silakan perbaiki kesalahan berikut:<br>";
        $html .= "<ul class='mb-0 mt-2'>";
        
        foreach ($errors as $error) {
            $html .= "<li>{$error}</li>";
        }
        
        $html .= "</ul>";
        $html .= "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
        $html .= "<span aria-hidden='true'>&times;</span>";
        $html .= "</button>";
        $html .= "</div>";
        
        return $html;
    }
}

if (!function_exists('format_date_indo')) {
    /**
     * Format tanggal ke format Indonesia
     * 
     * @param string $date Tanggal dalam format Y-m-d
     * @param bool $show_day Tampilkan hari atau tidak
     * @return string Tanggal dalam format Indonesia
     */
    function format_date_indo($date, $show_day = false)
    {
        if (!$date || $date == '0000-00-00') {
            return '-';
        }
        
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        
        $timestamp = strtotime($date);
        $day_name = $days[date('l', $timestamp)];
        $day = date('j', $timestamp);
        $month = $months[date('n', $timestamp)];
        $year = date('Y', $timestamp);
        
        if ($show_day) {
            return "{$day_name}, {$day} {$month} {$year}";
        }
        
        return "{$day} {$month} {$year}";
    }
}

if (!function_exists('format_phone_number')) {
    /**
     * Format nomor telepon Indonesia
     * 
     * @param string $phone Nomor telepon
     * @return string Nomor telepon yang diformat
     */
    function format_phone_number($phone)
    {
        if (!$phone) {
            return '-';
        }
        
        // Remove all non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // Format based on length
        if (strlen($phone) >= 10) {
            if (substr($phone, 0, 2) == '62') {
                // International format
                $phone = '+' . substr($phone, 0, 2) . ' ' . substr($phone, 2, 3) . '-' . substr($phone, 5, 4) . '-' . substr($phone, 9);
            } else {
                // Local format
                $phone = substr($phone, 0, 4) . '-' . substr($phone, 4, 4) . '-' . substr($phone, 8);
            }
        }
        
        return $phone;
    }
}

if (!function_exists('status_badge')) {
    /**
     * Generate status badge HTML
     * 
     * @param string $status Status value
     * @param array $config Configuration array
     * @return string HTML badge
     */
    function status_badge($status, $config = [])
    {
        $default_config = [
            'PNS' => ['class' => 'badge-success', 'text' => 'PNS'],
            'PPPK' => ['class' => 'badge-info', 'text' => 'PPPK'],
            'PTT' => ['class' => 'badge-warning', 'text' => 'PTT'],
            'Honorer' => ['class' => 'badge-secondary', 'text' => 'Honorer'],
            'Kontrak' => ['class' => 'badge-primary', 'text' => 'Kontrak'],
            'Magang' => ['class' => 'badge-light', 'text' => 'Magang'],
            'Volunteer' => ['class' => 'badge-dark', 'text' => 'Volunteer'],
        ];
        
        $config = array_merge($default_config, $config);
        
        if (!isset($config[$status])) {
            return "<span class='badge badge-secondary'>{$status}</span>";
        }
        
        $class = $config[$status]['class'];
        $text = $config[$status]['text'];
        
        return "<span class='badge {$class}'>{$text}</span>";
    }
}

if (!function_exists('gender_label')) {
    /**
     * Convert gender code to label
     * 
     * @param string $gender Gender code (L/P)
     * @return string Gender label
     */
    function gender_label($gender)
    {
        $labels = [
            'L' => 'Laki-laki',
            'P' => 'Perempuan'
        ];
        
        return $labels[$gender] ?? '-';
    }
}

if (!function_exists('truncate_text')) {
    /**
     * Truncate text with ellipsis
     * 
     * @param string $text Text to truncate
     * @param int $length Maximum length
     * @return string Truncated text
     */
    function truncate_text($text, $length = 50)
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . '...';
    }
}