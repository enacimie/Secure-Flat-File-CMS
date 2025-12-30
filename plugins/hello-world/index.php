<?php

use App\Core\Hook;

// 1. Hook into HEAD to add custom styles
Hook::add('head', function() {
    echo '<style>img { border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }</style>';
});

// 2. Hook into CONTENT to append a signature
Hook::add('content_html', function($html) {
    return $html . '<p class="text-xs text-gray-400 italic mt-4 border-t pt-2">✨ Enhanced by Hello World Plugin</p>';
});
