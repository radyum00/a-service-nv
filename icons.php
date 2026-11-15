<?php
function icon(string $name, int $size = 24, string $class = ''): string
{
    static $paths = [
        'washer'     => '<rect x="4" y="2.75" width="16" height="18.5" rx="3.25"/><path d="M4 7h16"/><circle cx="12" cy="13.5" r="4.25"/><path d="M7.5 5h.01M10.5 5h.01"/>',
        'clock'      => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 1.8"/>',
        'shield'     => '<path d="M12 2.9 19 5.7v5.1c0 4.6-3 7.7-7 9.3-4-1.6-7-4.7-7-9.3V5.7L12 2.9Z"/><path d="m9 11.9 2.1 2.1 4.1-4.1"/>',
        'truck'      => '<path d="M3 6.5h10.5v10H3zM13.5 10h3.2l3.3 3.3v3.2h-6.5z"/><circle cx="7" cy="18" r="1.8"/><circle cx="17.5" cy="18" r="1.8"/>',
        'calendar'   => '<rect x="3.5" y="5" width="17" height="16" rx="3"/><path d="M7.5 3v4M16.5 3v4M3.5 9.5h17"/><path d="m9 14.5 2 2 4-4"/>',
        'wrench'     => '<path d="M14.7 6.3a4.4 4.4 0 0 0-5.5 5.5l-5.3 5.3a2.1 2.1 0 0 0 3 3l5.3-5.3a4.4 4.4 0 0 0 5.5-5.5l-2.6 2.6-3-3 2.6-2.6Z"/>',
        'sparkles'   => '<path d="m12 3 1.4 3.8L17 8.2l-3.6 1.4L12 13.4l-1.4-3.8L7 8.2l3.6-1.4L12 3Z"/><path d="m18.5 14 .8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8.8-2.2Z"/>',
        'check'      => '<path d="m4.5 12.5 4.5 4.5L19.5 6.5"/>',
        'star'       => '<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3l-5.6 2.9L7.5 14 3 9.6l6.2-.9L12 3Z"/>',
        'phone'      => '<path d="M8.2 3.5 5.5 4.8c-.8.4-1.2 1.3-1 2.2.9 5.7 5.8 10.6 11.5 11.5.9.2 1.8-.2 2.2-1l1.3-2.7-4.2-2-1.1 2.2c-2.4-1.1-4.3-3-5.4-5.4l2.2-1.1-2-4.2Z"/>',
        'mapPin'     => '<path d="M20 10.2c0 5-8 11.3-8 11.3S4 15.2 4 10.2a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.7"/>',
        'arrowRight' => '<path d="M4.5 12h15M14 6.5l5.5 5.5L14 17.5"/>',
        'chevronDown'=> '<path d="m6 9 6 6 6-6"/>',
        'externalLink'=> '<path d="M14 4h6v6M20 4l-8.5 8.5"/><path d="M19 13.5v5A1.5 1.5 0 0 1 17.5 20h-11A1.5 1.5 0 0 1 5 18.5v-11A1.5 1.5 0 0 1 6.5 6h5"/>',
        'info'       => '<circle cx="12" cy="12" r="8.5"/><path d="M12 11v5M12 7.8h.01"/>',
        'vk'         => '<path fill="currentColor" stroke="none" d="M13.1 17.4c-5.5 0-8.7-3.7-8.8-9.9h2.8c.1 4.5 2.1 6.5 3.7 6.9V7.5h2.6v3.9c1.6-.2 3.2-1.9 3.8-3.9h2.6c-.4 2.4-2.1 4.4-3.3 5.1 1.2.6 3.1 2.4 3.9 4.8h-2.9c-.6-1.7-2-3-3.9-3.3v3.3h-.5Z"/>',
    ];

    $p = $paths[$name] ?? $paths['sparkles'];
    return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size
        . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" '
        . 'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="' . htmlspecialchars($class) . '">'
        . $p . '</svg>';
}
