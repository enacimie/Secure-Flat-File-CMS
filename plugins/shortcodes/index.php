<?php
use App\Core\Hook;

/**
 * Shortcode UI Plugin
 * Adds rich components to Markdown via Hook 'content_raw'.
 */

Hook::add('content_raw', function($markdown) {
    
    // 1. ALERTS
    // Usage: [alert type="info|success|warning|danger"]Message[/alert]
    $markdown = preg_replace_callback('/[alert type="?(.*?)"?](.*?)[\]/alert]/s', function($matches) {
        $type = $matches[1];
        $content = trim($matches[2]);
        
        $colors = [
            'info' => 'bg-blue-50 text-blue-800 border-blue-200',
            'success' => 'bg-green-50 text-green-800 border-green-200',
            'warning' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
            'danger' => 'bg-red-50 text-red-800 border-red-200',
            'neutral' => 'bg-gray-50 text-gray-800 border-gray-200'
        ];
        
        $class = $colors[$type] ?? $colors['neutral'];
        
        return "<div class=\"p-4 mb-4 rounded-md border $class\">
                    <div class=\"flex\">
                        <div class=\"flex-shrink-0\">
                            <svg class=\"h-5 w-5\" viewBox=\"0 0 20 20\" fill=\"currentColor\"><path fill-rule=\"evenodd\" d=\"M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z\" clip-rule=\"evenodd\"/></svg>
                        </div>
                        <div class=\"ml-3\">
                            <p class=\"text-sm font-medium\">$content</p>
                        </div>
                    </div>
                </div>";
    }, $markdown);

    // 2. BUTTONS
    // Usage: [button url="/contact" color="primary"]Click Me[/button]
    $markdown = preg_replace_callback('/[button url="?(.*?)"?(?: color="?(.*?)"?)?](.*?)[\]/button]/s', function($matches) {
        $url = $matches[1];
        $colorType = $matches[2] ?? 'primary';
        $text = $matches[3];

        $colors = [
            'primary' => 'bg-indigo-600 hover:bg-indigo-700 text-white',
            'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
            'outline' => 'bg-transparent border border-indigo-600 text-indigo-600 hover:bg-indigo-50'
        ];
        $colorClass = $colors[$colorType] ?? $colors['primary'];

        return "<a href=\"$url\" class=\"inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors $colorClass no-underline\">$text</a>";
    }, $markdown);

    // 3. YOUTUBE
    // Usage: [youtube id="VIDEO_ID"]
    $markdown = preg_replace_callback('/[youtube id="?(.*?)"?]/', function($matches) {
        $id = $matches[1];
        return "<div class=\"relative w-full aspect-video mb-6 rounded-lg overflow-hidden shadow-lg\">
                    <iframe class=\"absolute top-0 left-0 w-full h-full\" src=\"https://www.youtube.com/embed/$id\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>
                </div>";
    }, $markdown);

    // 4. GRID (2 Columns)
    // Usage: [grid][col]Left[/col][col]Right[/col][/grid]
    // Note: This is a bit complex for regex, simpler approach:
    // We just replace [grid] with a div wrapper, and [col] with div items.
    
    $markdown = str_replace('[grid]', '<div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">', $markdown);
    $markdown = str_replace('[/grid]', '</div>', $markdown);
    
    $markdown = str_replace('[col]', '<div>', $markdown);
    $markdown = str_replace('[/col]', '</div>', $markdown);

    // 5. CARD
    // Usage: [card title="Optional Title"]Content[/card]
    $markdown = preg_replace_callback('/\[card(?: title="?(.*?)"?)?\](.*?)\[\/card\]/s', function($matches) {
        $title = !empty($matches[1]) ? "<h3 class=\"text-lg font-bold mb-2 border-b pb-2\">{$matches[1]}</h3>" : '';
        $content = trim($matches[2]);
        return "<div class=\"bg-white shadow-sm border border-gray-200 rounded-lg p-6 my-4 hover:shadow-md transition-shadow\">
                    $title
                    <div class=\"prose-sm\">$content</div>
                </div>";
    }, $markdown);

    // 6. BADGE
    // Usage: [badge color="red|blue|green|gray"]Text[/badge]
    $markdown = preg_replace_callback('/\[badge(?: color="?(.*?)"?)?\](.*?)\[\/badge\]/', function($matches) {
        $color = $matches[1] ?? 'gray';
        $text = $matches[2];
        $colors = [
            'red' => 'bg-red-100 text-red-800',
            'blue' => 'bg-blue-100 text-blue-800',
            'green' => 'bg-green-100 text-green-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'purple' => 'bg-purple-100 text-purple-800',
            'gray' => 'bg-gray-100 text-gray-800'
        ];
        $class = $colors[$color] ?? $colors['gray'];
        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $class\">$text</span>";
    }, $markdown);

    // 7. DETAILS (Accordion)
    // Usage: [details summary="Click to expand"]Hidden Content[/details]
    $markdown = preg_replace_callback('/\[details summary="?(.*?)"?\](.*?)\[\/details\]/s', function($matches) {
        $summary = $matches[1] ?? 'Details';
        $content = trim($matches[2]);
        return "<details class=\"group my-4 border border-gray-200 rounded-lg overflow-hidden\">
                    <summary class=\"cursor-pointer bg-gray-50 p-4 font-medium text-gray-900 group-open:bg-gray-100 transition-colors select-none flex justify-between items-center\">
                        <span>$summary</span>
                        <span class=\"transform group-open:rotate-180 transition-transform\">▼</span>
                    </summary>
                    <div class=\"p-4 bg-white border-t border-gray-200\">$content</div>
                </details>";
    }, $markdown);

    // 8. HERO BANNER
    // Usage: [hero]Text[/hero]
    $markdown = preg_replace_callback('/\[hero\](.*?)\[\/hero\]/s', function($matches) {
        $content = trim($matches[1]);
        return "<div class=\"bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-8 md:p-12 text-center text-white my-8 shadow-lg\">
                    <h2 class=\"text-3xl md:text-4xl font-extrabold mb-0 text-white\">$content</h2>
                </div>";
    }, $markdown);

    // 9. DIVIDER
    // Usage: [divider]
    $markdown = str_replace('[divider]', '<hr class="my-8 border-t-2 border-gray-100">', $markdown);

    // 10. STAT / COUNTER
    // Usage: [stat value="100%" label="Uptime"]
    $markdown = preg_replace_callback('/\[stat value="?(.*?)"? label="?(.*?)"?\]/', function($matches) {
        return "<div class=\"text-center p-4 border border-gray-100 rounded-lg bg-white\">
                    <div class=\"text-3xl font-bold text-indigo-600\">{$matches[1]}</div>
                    <div class=\"text-xs font-bold text-gray-500 uppercase tracking-wide mt-1\">{$matches[2]}</div>
                </div>";
    }, $markdown);

    // 11. PRICING CARD
    // Usage: [price plan="Basic" cost="$0" period="/mo" button="Sign Up"]Features[/price]
    $markdown = preg_replace_callback('/\[price plan="?(.*?)"? cost="?(.*?)"? period="?(.*?)"? button="?(.*?)"?\](.*?)\[\/price\]/s', function($matches) {
        $plan = $matches[1];
        $cost = $matches[2];
        $period = $matches[3];
        $btnText = $matches[4];
        $features = trim($matches[5]);
        
        return "<div class=\"flex flex-col p-6 bg-white text-center rounded-lg border border-gray-200 shadow-sm hover:shadow-lg transition-shadow hover:border-indigo-300\">
                    <h3 class=\"text-lg font-medium text-gray-900\">$plan</h3>
                    <div class=\"mt-4 flex items-baseline justify-center\">
                        <span class=\"text-4xl font-extrabold text-gray-900\">$cost</span>
                        <span class=\"ml-1 text-xl text-gray-500\">$period</span>
                    </div>
                    <div class=\"mt-6 prose-sm text-gray-500\">$features</div>
                    <a href=\"#\" class=\"mt-8 block w-full bg-indigo-600 border border-transparent rounded-md py-2 text-sm font-semibold text-white text-center hover:bg-indigo-700\">$btnText</a>
                </div>";
    }, $markdown);

    // 12. PROGRESS BAR
    // Usage: [progress value="50" color="green"]
    $markdown = preg_replace_callback('/\[progress value="?(.*?)"?(?: color="?(.*?)"?)?\]/', function($matches) {
        $val = intval($matches[1]);
        $color = $matches[2] ?? 'indigo';
        $colors = ['green'=>'bg-green-600','red'=>'bg-red-600','blue'=>'bg-blue-600','indigo'=>'bg-indigo-600'];
        $bg = $colors[$color] ?? 'bg-indigo-600';
        return "<div class=\"w-full bg-gray-200 rounded-full h-2.5 my-4\">
                    <div class=\"$bg h-2.5 rounded-full\" style=\"width: {$val}%\"></div>
                </div>";
    }, $markdown);

    // 13. USER / TESTIMONIAL
    // Usage: [user name="Name" role="CEO" img="url"]Comment[/user]
    $markdown = preg_replace_callback('/\[user name="?(.*?)"? role="?(.*?)"? img="?(.*?)"?\](.*?)\[\/user\]/s', function($matches) {
        $name = $matches[1];
        $role = $matches[2];
        $img = $matches[3];
        $text = trim($matches[4]);
        
        // Fallback initials if no image
        $avatar = $img 
            ? "<img class=\"h-10 w-10 rounded-full object-cover\" src=\"$img\" alt=\"\">"
            : "<div class=\"h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center font-bold text-gray-600\">" . substr($name, 0, 1) . "</div>";

        return "<div class=\"flex items-center space-x-4 p-4 bg-gray-50 rounded-lg border border-gray-100\">
                    <div class=\"flex-shrink-0\">$avatar</div>
                    <div class=\"flex-1 min-w-0\">
                        <p class=\"text-sm font-medium text-gray-900 truncate\">$name</p>
                        <p class=\"text-xs text-gray-500 truncate\">$role</p>
                        <p class=\"text-sm text-gray-600 mt-1 italic\">\"$text\"</p>
                    </div>
                </div>";
    }, $markdown);

    // 14. ICONS (SVG)
    // Usage: [icon name="check"]
    $markdown = preg_replace_callback('/\[icon name="?(.*?)"?\]/', function($matches) {
        $name = $matches[1];
        $icons = [
            'check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
            'star' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />',
            'user' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
            'heart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />',
            'settings' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>'
        ];
        
        if (!isset($icons[$name])) return '';
        
        return "<svg class=\"w-5 h-5 inline-block text-gray-500\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">{$icons[$name]}</svg>";
    }, $markdown);

    return $markdown;
});

