<?php

if (!function_exists('ui_icon')) {
    function ui_icon($name, $class = '')
    {
        $icons = [
            'add' => '<path d="M12 5v14M5 12h14"/>',
            'arrow-left' => '<path d="M19 12H5"/><path d="m12 19-7-7 7-7"/>',
            'ban' => '<circle cx="12" cy="12" r="9"/><path d="M8.5 8.5l7 7"/>',
            'bars-3' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
            'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
            'car' => '<path d="M3 14l2-6a2 2 0 0 1 1.9-1.4h10.2A2 2 0 0 1 19 8l2 6"/><path d="M5 14h14v3a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1v-1H8v1a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-3Z"/><circle cx="7.5" cy="14.5" r="1.5"/><circle cx="16.5" cy="14.5" r="1.5"/>',
            'car-search' => '<path d="M2.5 13.5 4.3 8A2 2 0 0 1 6.2 6.5h8.7A2 2 0 0 1 16.8 8l1.5 4.5"/><path d="M4.5 13.5h9"/><circle cx="6.5" cy="14.5" r="1.2"/><circle cx="12.5" cy="14.5" r="1.2"/><circle cx="18" cy="17" r="3"/><path d="m20.2 19.2 1.8 1.8"/>',
            'chart-bar' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20v-4"/>',
            'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.5 2.5 4.5-5"/>',
            'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
            'clipboard' => '<rect x="5" y="4" width="14" height="16" rx="2"/><path d="M9 4.5h6a1.5 1.5 0 0 0-3 0H9Z"/>',
            'edit' => '<path d="M4 20h4l10-10-4-4L4 16v4Z"/><path d="m12 6 4 4"/>',
            'factory' => '<path d="M3 20V9l6 3V9l6 3V4l6 4v12Z"/><path d="M7 20v-4M11 20v-4M15 20v-4"/>',
            'fuel' => '<path d="M6 20V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v14Z"/><path d="M14 8h2l2 2v5a2 2 0 0 0 4 0V9"/><path d="M8 8h4"/>',
            'lock' => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 1 1 8 0v3"/>',
            'lock-open' => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 7-2"/>',
            'logout' => '<path d="M10 17v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-6a2 2 0 0 0-2 2v1"/><path d="M15 12H3"/><path d="m6 9-3 3 3 3"/>',
            'map-pin' => '<path d="M12 21s6-5.3 6-11a6 6 0 1 0-12 0c0 5.7 6 11 6 11Z"/><circle cx="12" cy="10" r="2.5"/>',
            'pencil-square' => '<path d="M3 21h6"/><path d="M14.5 4.5 19.5 9.5"/><path d="M12 7 5 14v5h5l7-7-5-5Z"/>',
            'refresh-cw' => '<path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/>',
            'search' => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 5 5"/>',
            'settings' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v2.5M12 19.5V22M4.9 4.9l1.8 1.8M17.3 17.3l1.8 1.8M2 12h2.5M19.5 12H22M4.9 19.1l1.8-1.8M17.3 6.7l1.8-1.8"/>',
            'trash' => '<path d="M4 7h16"/><path d="M10 11v6M14 11v6"/><path d="M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12"/><path d="M9 7V4h6v3"/>',
            'trophy' => '<path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 4h10v4a5 5 0 0 1-10 0Z"/><path d="M7 6H4a2 2 0 0 0 0 4h3M17 6h3a2 2 0 0 1 0 4h-3"/>',
            'user' => '<circle cx="12" cy="8" r="4"/><path d="M5 20a7 7 0 0 1 14 0"/>',
            'users' => '<path d="M16 21v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1"/><circle cx="9.5" cy="8" r="3.5"/><path d="M20 21v-1a4 4 0 0 0-3-3.9"/><path d="M15.5 4.8a3.5 3.5 0 0 1 0 6.4"/>',
            'view' => '<path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"/><circle cx="12" cy="12" r="3"/>',
            'wallet' => '<path d="M3 7.5A2.5 2.5 0 0 1 5.5 5H19a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5.5A2.5 2.5 0 0 1 3 16.5Z"/><path d="M3 8h16"/><path d="M16 13h3"/>',
            'wrench' => '<path d="M14 6a4 4 0 0 0 5 5l-8 8a2 2 0 1 1-3-3l8-8a4 4 0 0 0-2-2Z"/>',
            'x-circle' => '<circle cx="12" cy="12" r="9"/><path d="m9 9 6 6M15 9l-6 6"/>',
        ];

        $paths = $icons[$name] ?? $icons['car'];
        $classAttr = $class !== '' ? ' class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"' : '';

        return '<svg' . $classAttr . ' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="1em" height="1em" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" style="vertical-align:-0.125em;">' . $paths . '</svg>';
    }
}